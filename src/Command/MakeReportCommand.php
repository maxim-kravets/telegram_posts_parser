<?php

namespace App\Command;

use App\Entity\Keyword;
use App\Repository\KeywordRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use XLSXWriter;

class MakeReportCommand extends Command
{
    protected static $defaultName = 'app:make-report';
    private $keywordRepository;

    public function __construct(
        KeywordRepositoryInterface $keywordRepository
    ) {
        parent::__construct();
        $this->keywordRepository = $keywordRepository;
    }

    protected function configure()
    {
        $this->setDescription('Makes xls report');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        do {
            $key = trim((string) $io->ask('Enter key: '));

            if (empty($key)) {
                $io->error('Key can\'t be empty!');
            } else {
                /**
                 * @var Keyword $keyword
                 */
                $keyword = $this->keywordRepository->getByName($key);

                if (empty($keyword)) {
                    $io->error('There is no such key in the database. Enter another.');
                }
            }
        } while (empty($key) || empty($keyword));

        $posts = $keyword->getPosts();

        $result = [];
        foreach ($posts as $post) {
            $text_id = $post->getText()->getId();

            if (isset($result[$text_id])) {
                ++$result[$text_id]['count'];
                if ($post->getDate() > $result[$text_id]['last_date']) {
                    $result[$text_id]['last_date'] = $post->getDate();
                    $result[$text_id]['post_link'] = $this->generatePostLink($post->getTelegramId(), $post->getChatName());
                }
            } else {
                if ($post->getUser()->isDeleted()) {
                    $username = 'Аккаунт удален';
                } else {
                    $username = $post->getUser()->getFirstname().' '.$post->getUser()->getLastName().' ';

                    if (!empty($post->getUser()->getUsername())) {
                        $username .= '('.$post->getUser()->getUsername().')';
                    }
                }

                $result[$text_id] = [
                    'count' => 1,
                    'last_date' => $post->getDate(),
                    'post_link' => $this->generatePostLink($post->getTelegramId(), $post->getChatName()),
                    'text' => $post->getText()->getText(),
                    'username' => $username,
                ];
            }
        }

        $data[] = ['Username', 'Text', 'Count of posts', 'Link'];
        foreach ($result as $row) {
            $data[] = [$row['username'], $row['text'], $row['count'], $row['post_link']];
        }

        if (!file_exists('var/reports')) {
            mkdir('var/reports');
        }

        $xls = new XLSXWriter();
        $xls->writeSheet($data);
        $xls->writeToFile('var/reports/report.xls');

        $io->success('Report is ready!');

        return 0;
    }

    protected function generatePostLink(int $post_id, string $chat_name): string
    {
        return 'https://t.me/'.$chat_name.'/'.$post_id;
    }
}
