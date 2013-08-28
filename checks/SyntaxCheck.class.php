<?php
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'BasePreCommitCheck.class.php';

class SyntaxCheck extends BasePreCommitCheck 
{
  static private $has_php_binary = null;
  
  public function getTitle()
  {
    return "Reject files with syntax error";
  }

  public function renderErrorSummary()
  {
    return "Syntax error found";
  }

  public function checkFullFile($lines, $filename)
  {
    if ( $this->hasOption('no-syntax') ){
      return;
    }

    $this->hasPhpBinary();
    
    if ($this->getExtension($filename) !== 'php') {
      return;
    }
    
    passthru('svnlook cat '.escapeshellarg($this->repoName).' '.escapeshellarg($filename).' -t '.escapeshellarg($this->trxNum).' | php -l', $return);
    
    if ($return !== 0) {
      return 'Syntax error in '.$filename;
    }
  }
  
  
  private function hasPhpBinary()
  {
    if (self::$has_php_binary === null) {
      passthru('php --version', $return);
      
      if ($return !== 0) {
        throw new Exception('Impossible to find PHP binary');
      }
      
      self::$has_php_binary = true;
    }
  }
  
  public function renderInstructions()
  {
    return "If you want to force commit with error syntax, add the parameter --no-syntax in your comment";
  }
}