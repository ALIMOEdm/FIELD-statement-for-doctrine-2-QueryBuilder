<?php
namespace AppBundle\DQL;

use Doctrine\ORM\Query\Lexer;

class Field extends \Doctrine\ORM\Query\AST\Functions\FunctionNode{

    public $firstDateExpression = null;
    public $arrExpression = null;
    public $unit = null;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->firstDateExpression = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);

        $parser->match(Lexer::T_IDENTIFIER);
        $partialFieldSet[] = $parser->getLexer()->token['value'];
        while ($parser->getLexer()->isNextToken(Lexer::T_COMMA)) {
            $parser->match(Lexer::T_COMMA);
            $parser->match(Lexer::T_IDENTIFIER);

            $field = $parser->getLexer()->token['value'];

            while ($parser->getLexer()->isNextToken(Lexer::T_DOT)) {
                $parser->match(Lexer::T_DOT);
                $parser->match(Lexer::T_IDENTIFIER);
                $field .= '.'.$parser->getLexer()->token['value'];
            }

            $partialFieldSet[] = $field;
        }


        $this->arrExpression = $partialFieldSet;
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {

        $a = $this->arrExpression;
        $str_s = implode(',', $a);
        $str = 'FIELD(';
        $str .= $this->firstDateExpression->dispatch($sqlWalker) . ', ' ;
        $str .= $str_s.')';
        return $str;
    }
}
