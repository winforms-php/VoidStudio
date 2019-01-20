namespace VoidEngine;

$vlf = <<<'VLF'

Form MainForm
    size: [$SCREEN->width / 4, $SCREEN->height / 3]
    startPosition: fspCenterScreen
    backgroundColor: clWhite

    caption: 'Form Caption'

    Button MainButton1
        bounds: [8, 8, 128, 32]

        caption: 'CLICK ME 1'

        ClickEvent:^ function ($self)
            {
                pre ($self->caption);
            }

    Button MainButton2
        bounds: [8, 48, 128, 32]

        caption: 'CLICK ME 2'

        Button MainButton3
            caption: 'CLICK ME 3'

            ClickEvent:^ function ($self)
                {
                    pre ($self->caption);
                }

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
pre (VLFInterpreter::run ($parser->tree));