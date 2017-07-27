<?
namespace Test;
use Test\ContentMonitor;

class ContentInverse extends ContentMonitor
{
    /**
     * Inverse replacement in final content
     *
     * @param  string $content final text after replacements
     * @param  array $replacements inverse data for replacements
     * @return string source text
     */
    public function replaceContentParts($content, $replacements)
    {        
        $inverseReplacements = array_flip($replacements);        
        $inverseReplacements = array_reverse($inverseReplacements, true);
                
        return print_r(parent::doReplace($content, $inverseReplacements));
    }
}