<?php

namespace App\Command;

use App\GoogleSheets;
use App\YandexTaxi as YandexTaxiApp;
use DomainException;
use Google\Auth\Cache\InvalidArgumentException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class YandexTaxi extends Command
{
    protected static $defaultName = 'yandextaxi:get-data';

    protected function configure()
    {
        $this
            ->setDescription('Получает данные из Яндекс.Такси')
            ->setHelp(
                'Используется для получения актуальной стоимости поездки по выбранному направлению, по умолчанию - "дом -> работа"'
            );

        $this
            ->addOption(
                'direction',
                'd',
                InputOption::VALUE_REQUIRED,
                'Направление поездки',
                'to_work'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $log = new Logger('cli');
        $log->pushHandler(new StreamHandler(__DIR__ . '/../../storage/logs/logger.log', Logger::DEBUG));

        $io = new SymfonyStyle($input, $output);
        $direction = $input->getOption('direction');

        $io->title($this->getDescription());
        $io->text($this->getHelp());
        $io->newLine();

        if ($direction == 'to_work') {
            $range = env('GOOGLE_SPREADSHEET_ID_TO_WORK_RANGE');
        } elseif ($direction == 'to_home') {
            $range = env('GOOGLE_SPREADSHEET_ID_TO_HOME_RANGE');
        } else {
            $log->warning('Wrong direction!', ['source' => __FILE__ . ':' . __LINE__, 'direction' => $direction]);
            throw new \RuntimeException('Ошибка значения переменной direction');
        }

        $yandex_taxi = new YandexTaxiApp($direction);

        try {
            if (env('GOOGLE_SPREADSHEET_PUSH') === "true") {
                $data_to_export = $yandex_taxi->getPrepareArrayToExport();
            } else {
                $data_to_export = $yandex_taxi->getDataToExport();
            }
        } catch (DomainException $e) {
            $log->critical($e->getMessage(), ['source' => __FILE__ . ':' . __LINE__, 'direction' => $direction]);
            throw new \RuntimeException($e->getMessage());
        }

        if (env('GOOGLE_SPREADSHEET_PUSH') === "true") {
            $values = array_values($data_to_export);

            $requestBody = GoogleSheets::makeValueRange($range, $values);
            $service = GoogleSheets::getService();

            try {
                $response = $service->spreadsheets_values->append(
                    env('GOOGLE_SPREADSHEET_ID'),
                    $range,
                    $requestBody,
                    ['valueInputOption' => 'USER_ENTERED']
                );
            } catch (InvalidArgumentException $e) {
                $log->critical($e->getMessage(), ['source' => __FILE__ . ':' . __LINE__, 'direction' => $direction]);
                throw new \RuntimeException($e->getMessage());
            }
        }

        $table_rows = [];

        foreach ($data_to_export as $index => $item) {
            $table_rows[] = [$index, $item];
        }

        $io->table(
            ['Параметр', 'Значение'],
            $table_rows
        );

        return Command::SUCCESS;
    }
}