<?
namespace Test;

class ContentMonitor
{
    protected $url, $replacements = array(), $result = '', $cycleCount = 0, $maxCycleCount = 20;

    public function __construct($url)
    {
        if (filter_var($url, FILTER_VALIDATE_URL))
            $this->url = $url;
        else
            die('This url is not valid');
    }
    
    /**
     * Get content from current URL
     *
     * @param  string $url page url
     * @return string page content
     */
    public static function getContentFromUrl($url)
    {
        $c = curl_init();
        
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($c, CURLOPT_TIMEOUT, 60);

        $content = curl_exec($c);

        if (curl_error($c))
            return curl_error($c);

        curl_close($c);
        return $content;
    }
    
    /**
     * Update replacement data by array
     *
     * @param  array $replaceInfoArray new data
     * @return array ready data for changes
     */
    public function replacingMultiplyData($replaceInfoArray)
    {
        $this->replacements = array_merge($this->replacements, $replaceInfoArray);
        return $this->replacements;
    }
    
    /**
     * Update replacement data by 2 strings
     *
     * @param  string $search changeable text
     * @param  string $replace text for replacement
     * @return array ready data for changes
     */    
    public function replacingSimpleData($search, $replace)
    {
        if($search && strlen($search)>0)
            $this->replacements = array_merge($this->replacements, array($search => $replace));
        return $this->replacements;
    }
    
    /**
     * Replace concrete parts in the received content
     *
     * @param  string $content received page content
     * @param  array $replacements total data for text changes
     * @return string changed content
     */
    public function doReplace($content, $replacements)
    {
        $this->result = str_replace(array_keys($replacements), array_values($replacements), $content);
        
        if($this->cycleCount >= $this->maxCycleCount)
            return $this->result;
        
        foreach($replacements as $key => $value)
        {
            if(strpos($this->result, $key) !== false)
            {
                $this->cycleCount++;
                $this->doReplace($this->result);
            }
        }
        
        return $this->result;
    }
    
    /**
     * Get content, do changes
     *
     * @return string
     */
    public function replaceContentParts()
    {
        $content = self::getContentFromUrl($this->url);
        return $this->doReplace($content, $this->replacements);
    }
    
    /**
     * Print result
     *
     * @return string
     */
    public function printResult()
    {
        return print_r($this->result);
    }
}