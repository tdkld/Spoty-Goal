<?php
function render_template($template_path, $variables = array(), $print = false)
{
    $output = NULL;
    if(file_exists($template_path)){
        extract($variables);
        ob_start();
        include $template_path;
        $output = ob_get_clean();
    }
    if ($print) {
        print $output;
    }
    return $output;
}
?>