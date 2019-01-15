namespace VoidEngine;

$vlf = <<<'EOD'

Form MainForm
    CheckedListBox Box
        dock: dsFill

        ->items->addRange ([123, 321])

    ->show ()

EOD;

VLFReader::read ($vlf);