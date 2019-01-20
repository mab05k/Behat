<?php

/*
 * This file is part of the Behat.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Behat\Behat\Output\Node\Printer;

use Behat\Behat\Output\Node\Printer\Helper\ResultToStringConverter;
use Behat\Testwork\Output\Printer\OutputPrinter;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Behat counter printer.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
final class CounterPrinter
{
    /**
     * @var ResultToStringConverter
     */
    private $resultConverter;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Initializes printer.
     *
     * @param ResultToStringConverter $resultConverter
     * @param TranslatorInterface     $translator
     */
    public function __construct(ResultToStringConverter $resultConverter, TranslatorInterface $translator)
    {
        $this->resultConverter = $resultConverter;
        $this->translator = $translator;
    }

    /**
     * Prints scenario and step counters.
     *
     * @param OutputPrinter $printer
     * @param string        $intro
     * @param array         $stats
     */
    public function printCounters(OutputPrinter $printer, $intro, array $stats)
    {
        $stats = array_filter($stats, function ($count) { return 0 !== $count; });

        if (0 === count($stats)) {
            $totalCount = 0;
        } else {
            $totalCount = array_sum($stats);
        }

        $detailedStats = array();
        foreach ($stats as $resultCode => $count) {
            $style = $this->resultConverter->convertResultCodeToString($resultCode);

            $transId = $style . '_count';
            $message = $this->translator->trans($transId, array('%count%' => $count, '%1%' => $count), 'output');

            $detailedStats[] = sprintf('{+%s}%s{-%s}', $style, $message, $style);
        }

        $message = $this->translator->trans($intro, array('%count%' => $totalCount, '%1%' => $totalCount), 'output');
        $printer->write($message);

        if (count($detailedStats)) {
            $printer->write(sprintf(' (%s)', implode(', ', $detailedStats)));
        }

        $printer->writeln();
    }
}
