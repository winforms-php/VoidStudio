namespace VoidEngine;

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