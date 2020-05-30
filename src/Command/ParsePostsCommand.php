<?php

namespace App\Command;

use App\Dto\Keyword as KeywordDto;
use App\Dto\Post as PostDto;
use App\Dto\PostText as PostTextDto;
use App\Dto\User as UserDto;
use App\Entity\Keyword;
use App\Entity\Post;
use App\Entity\PostText;
use App\Entity\User;
use App\Repository\KeywordRepositoryInterface;
use App\Repository\PostRepositoryInterface;
use App\Repository\PostTextRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Service\TelegramInterface;
use danog\MadelineProto\Exception;
use danog\MadelineProto\RPCErrorException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ParsePostsCommand extends Command
{
    protected static $defaultName = 'app:parse-posts';

    private $telegramService;
    private $postRepository;
    private $userRepository;
    private $keywordRepository;
    private $postTextRepository;

    public function __construct(
        TelegramInterface $telegramService,
        PostRepositoryInterface $postRepository,
        UserRepositoryInterface $userRepository,
        KeywordRepositoryInterface $keywordRepository,
        PostTextRepositoryInterface $postTextRepository
    ) {
        parent::__construct();
        $this->telegramService = $telegramService->getAPI();
        $this->postRepository = $postRepository;
        $this->userRepository = $userRepository;
        $this->keywordRepository = $keywordRepository;
        $this->postTextRepository = $postTextRepository;
    }

    protected function configure()
    {
        $this->setDescription('Allows you to parse chat posts.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->telegramService->async(true);

        $this->telegramService->loop(function () {
            if (! yield $this->telegramService->getSelf()) {
                $phone = trim((string) readline(PHP_EOL.'Enter phone: '));
                yield $this->telegramService->phoneLogin($phone);

                $code = trim((string) readline(PHP_EOL.'Enter code: '));
                yield $this->telegramService->completePhoneLogin($code);
            }
        });

        do {
            $chat = trim((string) readline(PHP_EOL.'Enter chat: '));

            $chat_info = [];
            $is_chat_exists = true;
            if (empty($chat)) {
                $io->error('Chat can\'t be empty!');
            } else {
                $this->telegramService->loop(function () use ($chat, $io, &$is_chat_exists, &$chat_info) {
                    try {
                        $chat_info = yield $this->telegramService->getInfo($chat);
                    } catch (\Exception $e) {
                        $io->error('There is no such chat!');
                        $is_chat_exists = false;
                    }
                });
            }
        } while (empty($chat) || !$is_chat_exists);

        do {
            $key = trim((string) readline(PHP_EOL.'Enter key: '));

            if (empty($key)) {
                $io->error('Key can\'t be empty!');
            }
        } while (empty($key));

        do {
            $days = readline(PHP_EOL.'Enter depth(days): ');

            $is_int = preg_match('/^[0-9]+$/', $days);

            if (!$is_int) {
                $io->error('Depth can\'t be empty and must contain only numbers!');
            }
        } while (!$is_int);

        try {
            $date = new \DateTime(date('Y/m/d'));

            if ($days > 0) {
                $date->sub(new \DateInterval('P'.$days.'D'));
            }
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            die();
        }

        $unique_messages = [];
        $total_messages_count = 0;
        $timestamp = $date->getTimestamp();
        $this->telegramService->loop(function () use ($chat, $timestamp, $io, $key, $output, $chat_info, &$unique_messages, &$total_messages_count) {
            try {
                $message = yield $this->telegramService->messages->getHistory([
                    'peer' => $chat,
                    'offset_id' => 0,
                    'offset_date' => $timestamp,
                    'add_offset' => 0,
                    'limit' => 1,
                    'max_id' => 0,
                    'min_id' => 0,
                ])['messages'][0];
            } catch (RPCErrorException $e) {
                if ('CHANNEL_PRIVATE' === $e->rpc) {
                    try {
                        $this->telegramService->messages->importChatInvite([
                            'hash' => substr($chat, strrpos($chat, '/') + 1)
                        ]);

                        $message = yield $this->telegramService->messages->getHistory([
                            'peer' => $chat,
                            'offset_id' => 0,
                            'offset_date' => $timestamp,
                            'add_offset' => 0,
                            'limit' => 1,
                            'max_id' => 0,
                            'min_id' => 0,
                        ])['messages'][0];
                    } catch (\Exception $e) {
                        $io->error($e->getMessage());

                        die();
                    }
                } else {
                    $io->error($e->getMessage());

                    die();
                }
            } catch (Exception $e) {
                $io->error($e->getMessage());

                die();
            }

            $min_id = $message['id'];

            try {
                $message = yield $this->telegramService->messages->getHistory([
                    'peer' => $chat,
                    'offset_id' => 0,
                    'offset_date' => 0,
                    'add_offset' => 0,
                    'limit' => 1,
                    'max_id' => 0,
                    'min_id' => 0,
                ])['messages'][0];
            } catch (Exception $e) {
                $io->error($e->getMessage());

                die;
            }

            $max_id = $message['id'];

            $progressBar = new ProgressBar($output, ($max_id - $min_id));
            $progressBar->setFormat('<info>Progress: %current%/%max% (messages) [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%</info>');
            $progressBar->start();

            $offset_id = 0;

            do {
                try {
                    $messages = yield $this->telegramService->messages->getHistory([
                        'peer' => $chat,
                        'offset_id' => $offset_id,
                        'offset_date' => 0,
                        'add_offset' => 0,
                        'limit' => 100,
                        'max_id' => 0,
                        'min_id' => $min_id,
                    ])['messages'];
                } catch (Exception $e) {
                    $io->error($e->getMessage());

                    die();
                }

                // message filter by key
                foreach ($messages as $message) {
                    $progressBar->advance();

                    if ('message' !== $message['_']) {
                        continue;
                    }

                    $text = trim((string) $message['message']);

                    if (false !== stripos($text, $key)) {
                        ++$total_messages_count;

                        if (!in_array($text, $unique_messages)) {
                            $unique_messages[] = $text;
                        }

                        // get username
                        preg_match('/@[a-zA-z0-9_]*/', $text, $matches);
                        $username = $matches[0] ?? null;
                        $user_telegram_id = $message['from_id'] ?? null;

                        $user = $this->userRepository->getUserByTelegramId($user_telegram_id);

                        if (empty($user)) {
                            $userDto = new UserDto($username, $user_telegram_id);
                            $user = User::create($userDto);
                            $this->userRepository->save($user);
                        }

                        $keyword = $this->keywordRepository->getByName($key);

                        if (empty($keyword)) {
                            $keywordDto = new KeywordDto($key);
                            $keyword = Keyword::create($keywordDto);
                            $this->keywordRepository->save($keyword);
                        }

                        $postText = $this->postTextRepository->getPostText($text);

                        if (empty($postText)) {
                            $postTextDto = new PostTextDto($text);
                            $postText = PostText::create($postTextDto);
                            $this->postTextRepository->save($postText);
                        }

                        try {
                            $date = new \DateTime(date('Y/m/d H:m:s', $message['date']));
                        } catch (\Exception $e) {
                            $io->error($e->getMessage());
                        }

                        $post = $this->postRepository->getPostByTelegramId($message['id']);

                        if (empty($post)) {
                            $postDto = new PostDto($user, $keyword, $message['id'], $date, $chat_info['Chat']['id'], $postText);
                            $post = Post::create($postDto);
                            $this->postRepository->save($post);
                        }
                    }
                }

                if (!empty($messages)) {
                    $offset_id = $messages[array_key_last($messages)]['id'];
                }

                sleep(1);
            } while (!empty($messages));

            $progressBar->finish();
        });

        $this->telegramService->stop();

        $io->success(
            'Posts successfully parsed!'.PHP_EOL.
            'Total messages count: '.$total_messages_count.PHP_EOL.
            'Unique messages count: '.count($unique_messages)
        );

        return 0;
    }
}
