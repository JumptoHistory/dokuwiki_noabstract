<?php
/**
 * Plugin No Abstract: Exclude certain parts of a page from the abstract in metadata.
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Jumptohistory <jumptohistory@gmail.com>
 */

if(!defined('DOKU_INC')) die();

class syntax_plugin_noabstract extends DokuWiki_Syntax_Plugin {
    public function getType() { return 'container'; }
    public function getSort() { return 32; }
    public function getAllowedTypes() { return array('formatting', 'substition', 'disabled', 'protected', 'container', 'baseonly'); }   
    public function connectTo($mode) {
        $this->Lexer->addEntryPattern('<noabstract>(?=.*?</noabstract>)',$mode,'plugin_noabstract');
        $this->Lexer->addSpecialPattern('~~NOABSTRACT~~', $mode, 'plugin_noabstract');
    }
    public function postConnect() { $this->Lexer->addExitPattern('</noabstract>','plugin_noabstract'); }

    public function handle($match, $state, $pos, Doku_Handler $handler) {
        return array($state, $match);
    }

    protected $captureDefault;

    public function render($mode, Doku_Renderer $renderer, $data) {
        list($state,$match) = $data;
        if($mode == 'xhtml'){
            switch ($state) {
            case DOKU_LEXER_UNMATCHED:
                $renderer->doc .= $renderer->_xmlEntities($match);
                break;
            }
            return true;
        } else if($mode == 'metadata'){
            switch ($state) {
            case DOKU_LEXER_ENTER:
                $this->captureDefault = $renderer->capture;
                $renderer->capture = false;
                break;
            case DOKU_LEXER_SPECIAL:
               $renderer->capture = false;
               break;
            case DOKU_LEXER_EXIT:
                $renderer->capture = $this->captureDefault;
                break;
            }
            return true;
        }
        return false;
    }
}
