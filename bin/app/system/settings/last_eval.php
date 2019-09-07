namespace VoidEngine;

$vlf = <<<'VLF'

Form MainForm
    caption: 'Form Caption'

    Button MainButton
        bounds: [8, 8, 196, 32]
        caption: 'Click Me!'

        ClickEvent:^ function ()
          {
              pre ('Hello, World!');
          }

VLF;

$objects = VLFInterpreter::run (new VLFParser ($vlf, [
    'strong_line_parser'            => false,
    'ignore_postobject_info'        => true,
    'ignore_unexpected_method_args' => true,

    'use_caching' => false,
    'debug_mode'  => false
]));

$objects['MainForm']->showDialog ();