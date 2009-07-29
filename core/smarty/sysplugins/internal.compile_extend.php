<?php

/**
* Smarty Internal Plugin Compile extend
* 
* Compiles the {extend} tag
* 
* @package Smarty
* @subpackage Compiler
* @author Uwe Tews 
*/
/**
* Smarty Internal Plugin Compile extend Class
*/
class Smarty_Internal_Compile_Extend extends Smarty_Internal_CompileBase {
    /**
    * Compiles code for the {extend} tag
    * 
    * @param array $args array with attributes from parser
    * @param object $compiler compiler object
    * @return string compiled code
    */
    public function compile($args, $compiler)
    {
        $this->compiler = $compiler;
        $this->required_attributes = array('file'); 
        // check and get attributes
        $_attr = $this->_get_attributes($args);
        $_smarty_tpl = $compiler->template; 
        // $include_file = '';
        eval('$include_file = ' . $_attr['file'] . ';'); 
        // create template object
        $_template = new Smarty_Template ($include_file, $compiler->template); 
        // save file dependency
        $compiler->template->properties['file_dependency'][] = array($_template->getTemplateFilepath(), $_template->getTemplateTimestamp()); 
        // $_old_source = preg_replace ('/' . $this->smarty->left_delimiter . 'extend\s+(?:file=)?\s*(\S+?|(["\']).+?\2)' . $this->smarty->right_delimiter . '/i', '' , $compiler->template->template_source, 1);
        $_old_source = $compiler->template->template_source;
        if (preg_match_all('/(' . $this->compiler->smarty->left_delimiter . 'block(.+?)' . $this->compiler->smarty->right_delimiter . ')/', $_old_source, $dummy) !=
                preg_match_all('/(' . $this->compiler->smarty->left_delimiter . '\/block(.*?)' . $this->compiler->smarty->right_delimiter . ')/', $_old_source, $dummy)) {
            $this->compiler->trigger_template_error(" unmatched {block} {/block} pairs");
        } 
        $_old_source = preg_replace_callback('/(' . $this->compiler->smarty->left_delimiter . 'block(.+?)' . $this->compiler->smarty->right_delimiter . ')((?:\r?\n?)(.*?)(?:\r?\n?))(' . $this->compiler->smarty->left_delimiter . '\/block(.*?)' . $this->compiler->smarty->right_delimiter . ')/is', array($this, 'saveBlockData'), $_old_source);
        $compiler->template->template_source = $_template->getTemplateSource();
        $compiler->abort_and_recompile = true;
        return ' ';
    } 

    protected function saveBlockData(array $matches)
    {
        if (0 == preg_match('/(.?)(name=)([^ ]*)/', $matches[2], $_match)) {
            $this->compiler->trigger_template_error("\"" . $matches[0] . "\" missing name attribute");
        } else {
            // compile block content
            $_tpl = $this->compiler->smarty->createTemplate('string:' . $matches[3]);
            $_tpl->template_filepath = $this->compiler->template->getTemplateFilepath();
            $_tpl->suppressHeader = true;
            $_compiled_content = $_tpl->getCompiledTemplate();
            unset($_tpl);
            $_name = trim($_match[3], "\"'");

            if (isset($this->compiler->template->block_data[$_name])) {
                if ($this->compiler->template->block_data[$_name]['mode'] == 'prepend') {
                    $this->compiler->template->block_data[$_name]['compiled'] .= $_compiled_content;
                    $this->compiler->template->block_data[$_name]['source'] .= $matches[3];
                } elseif ($this->compiler->template->block_data[$_name]['mode'] == 'append') {
                    $this->compiler->template->block_data[$_name]['compiled'] = $_compiled_content . $this->compiler->template->block_data[$_name]['compiled'];
                    $this->compiler->template->block_data[$_name]['source'] = $matches[3] . $this->compiler->template->block_data[$_name]['source'];
                } 
            } else {
                $this->compiler->template->block_data[$_name]['compiled'] = $_compiled_content;
                $this->compiler->template->block_data[$_name]['source'] = $matches[3];
            } 
            // if (isset($this->compiler->template->block_data[$_name]['mode'])) {
            // if ($this->compiler->template->block_data[$_name]['mode'] != 'replace') {
            if (preg_match('/(.?)(append=true)(.*)/', $matches[2], $_match) != 0) {
                $this->compiler->template->block_data[$_name]['mode'] = 'append';
            } elseif (preg_match('/(.?)(prepend=true)(.*)/', $matches[2], $_match) != 0) {
                $this->compiler->template->block_data[$_name]['mode'] = 'prepend'; 
                // }
                // }
            } else {
                $this->compiler->template->block_data[$_name]['mode'] = 'replace';
            } 
        } 
    } 
} 

?>
