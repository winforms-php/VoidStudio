namespace VoidEngine; 

pre (Components::cleanJunk ());

$vlf = <<<'EOD'

Form MainForm
    Button Button
        caption: 'Button Yeah!'
        bounds: [16, 16, 128, 48]

        clickEvent:^ function ($self)
            {
                $msg = new WFClass ('System.Windows.Forms.MessageBox'); // new MessageBox;
                $msg->show ('test message');
            }

    ->show ()

EOD;

VLFReader::read ($vlf);