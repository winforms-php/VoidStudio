$code = <<<'CODE'

%VoidEngine%

CODE;

@eval ($code);

$vlf = <<<'VLF'

%vlf_imports%

VLF;

VLFInterpreter::$throw_errors = false;

$APPLICATION->run (VLFInterpreter::run (new VLFParser ($vlf, [
    'strong_line_parser'            => false,
    'ignore_postobject_info'        => true,
    'ignore_unexpected_method_args' => true,
    
    'use_caching' => false
]))['%entering_point%']);