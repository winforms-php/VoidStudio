namespace VoidEngine;

$tmp = <<<'VLF'

Form Form1
	ClientSize: [738, 423]
	Name: "Form1"
	Text: "Form1"

	Label Label1549179448249445
		Anchor: 15
		BorderStyle: 1
		Font: ["Segoe UI", 48]
		Location: [12, 9]
		Name: "Label1549179448249445"
		Size: [714, 405]
		TabIndex: 0
		Text: "ְבדהûך"
		TextAlign: 32

Form Form1
    ->show

VLF;

VLFInterpreter::run (new VLFParser ($tmp, [
    'strong_line_parser' => false,
    'ignore_postobject_info' => true,
    'ignore_unexpected_method_args' => true,

    'use_caching' => false,
    'debug_mode' => false
]));

/*$vlf = <<<'VLF'

Form MainForm
    size: [$SCREEN->width / 4, $SCREEN->height / 4]
    startPosition: fspCenterScreen
    backgroundColor: clWhite

    caption: 'Form Caption'

    Button MainButton
        bounds: [8, 8, 128, 32]

        caption: 'Click me'

        Button fewfwe
            caption: 'Sosaki Ken'

        Button fewfwefeef
            caption: 'Sosaki Ken 2!'

            y: 12
            w: 120

        ClickEvent:^ function ($self)
            {
                pre ($self->caption);
            }

    ->show

VLF;

pre (($parser = new VLFParser ($vlf, [
    'strong_line_parser' => false,
    'ignore_postobject_info' => true,
    'ignore_unexpected_method_args' => true,

    'use_caching' => false,
    'debug_mode' => false
]))->tree);

pre (VLFInterpreter::run ($parser->tree));*/