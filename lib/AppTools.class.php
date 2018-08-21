<?php

class AppTools
{
    public static function sendMail($from_email, $to_user, $subject, $body, $ip_address = null)
    {
        $message = sfContext::getInstance()->getMailer()->compose();
        $message->setSubject($subject);
        $message->setTo($to_user->getEmail());
        $message->setFrom($from_email);

        $message->setBody($body, 'text/html');

        if (sfContext::getInstance()->getMailer()->send($message)){
            return true;
        }

        return false;
    }


    public static function generateNumber($number)
    {
        settype($number, "integer");
        return $number + 1;
    }

    public static function validateFeed($sFeedURL)
    {
        $sValidator = 'http://feedvalidator.org/check.cgi?url=';

        if ($sValidationResponse = @file_get_contents($sValidator . urlencode($sFeedURL))) {
            if (stristr($sValidationResponse, 'This is a valid RSS feed') !== false) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function validateEmail($email)
    {
        // First, we check that there's one @ symbol, and that the lengths are right
        if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
            // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
            return false;
        }
        // Split it into sections to make life easier
        $email_array = explode("@", $email);
        $local_array = explode(".", $email_array[0]);
        for ($i = 0; $i < sizeof($local_array); $i++) {
            if (!ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
                return false;
            }
        }
        if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
            $domain_array = explode(".", $email_array[1]);
            if (sizeof($domain_array) < 2) {
                return false; // Not enough parts to domain
            }
            for ($i = 0; $i < sizeof($domain_array); $i++) {
                if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
                    return false;
                }
            }
        }
        return true;
    }

    public static function parseDictionaryFilename($word)
    {
        $out = ereg_replace("[^[:alpha:]]", "_", $word);
        return substr($out, 0, 20) . '.mp3';
    }

    public static function scaleImage(&$width, &$height, $to)
    {

        $hscale = $height / $to;
        $wscale = $width / $to;

        if (($hscale > 1) || ($wscale > 1)) {
            $scale = ($hscale > $wscale) ? $hscale : $wscale;
        } else {
            $scale = 1;
        }

        $width = floor($width / $scale);
        $height = floor($height / $scale);

        return array($width, $height, $scale);
    }

    public static function cropThumbnail($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale)
    {
        $ext = myTools::getFileExtension($image);
        $newImageWidth = ceil($width * $scale);
        $newImageHeight = ceil($height * $scale);
        $newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
        if ($ext == 'jpg') {
            $source = imagecreatefromjpeg($image);
        } else {
            $source = imagecreatefrompng($image);
        }

        $thumb_image_name = myTools::changeFileExtension($thumb_image_name, $ext, 'jpg');

        imagecopyresampled($newImage, $source, 0, 0, $start_width, $start_height, $newImageWidth, $newImageHeight, $width, $height);

        imagejpeg($newImage, $thumb_image_name, 90);

        chmod($thumb_image_name, 0777);

        return $thumb_image_name;
    }

    public static function getFileName($fileName)
    {
        return substr($fileName, strrpos($fileName, '/') + 1, strlen($fileName) - strrpos($fileName, '/'));
    }

    public static function getFileExtension($fileName)
    {
        return strtolower(substr(strrchr($fileName, '.'), 1));
    }

    public static function changeFileExtension($fileName, $old_ext, $new_ext)
    {
        return str_replace("." . $old_ext, "." . $new_ext, $fileName);
    }

    public static function utf8_substr($str, $from, $len)
    {
        # utf8 substr
        return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $from . '}' .
            '((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $len . '}).*#s',
            '$1', $str);
    }

    public static function getCurrentUrl()
    {
        return (!empty($_SERVER['HTTPS'])) ? "https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] : "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    }

    public static function mb_strlen($t, $encoding = 'UTF-8')
    {
        /* --enable-mbstring */
        if (function_exists('mb_strlen')) {
            return mb_strlen($t, $encoding);
        } else {
            return strlen(utf8_decode($t));
        }
    }

    public static function auto_link_image($text)
    {
        if (!function_exists("auto_image_tag")) {
            function auto_image_tag($src)
            {
                return $src[1] . '<a href="http' . $src[3] . "://" . $src[4] . $src[5] . '" target="_blank" rel="nofollow"><img src="http' . $src[3] . "://" . $src[4] . $src[5] . '" class="autoimgresize" /></a>' . $src[6];
            }

        }

        $regex = '/
        (                          # leading text
          <\w+.*?>|                # leading HTML tag, or
          [^=!:\'"\/]|               # leading punctuation, or
          ^                        # beginning of line
        )
        (
          (?:http(s)?:\/\/)|           # protocol spec, or
          (www\.)                # www.*
        )
        (
          [-\w]+                   # subdomain or domain
          (?:\.[-\w]+)*            # remaining subdomains or domain
          (?::\d+)?                # port
          (?:(?:\/(?:(?:[~\w\+%-]|(?:[,.;:][^\s$]))+)?)+(?i:\.(?:jpg|jpeg|gif|png))) # path
          (?:\?[\w\+%&=.;-]+)?     # query string
          (?:\#[\w\-]*)?           # trailing anchor
        )
        ([[:punct:]]|\s|<|$)       # trailing text
        /x';

        return preg_replace_callback($regex, "auto_image_tag", $text);

    }

    public static function stripText($text)
    {
        $text = strtolower($text);

        // strip all non word chars
        $text = preg_replace('/\W/', ' ', $text);

        // replace all white space sections with a dash
        $text = preg_replace('/\ +/', '-', $text);

        // trim dashes
        $text = preg_replace('/\-$/', '', $text);
        $text = preg_replace('/^\-/', '', $text);

        return $text;
    }

    public static function getSentences($str, $max_word_count = 30)
    {
        $sentences = explode('. ', strip_tags($str));
        $description = "";
        $counter = 0;

        foreach ($sentences as $sentence) {
            $words = explode(' ', $sentence);
            $counter += count($words);
            $description .= $sentence . '. ';
            if ($counter > $max_word_count) {
                return trim($description);
            }
        }

        return trim($description);
    }

    /**
     * Truncates text.
     *
     * Cuts a string to the length of $length and replaces the last characters
     * with the ending if the text is longer than length.
     *
     * @param string $text String to truncate.
     * @param integer $length Length of returned string, including ellipsis.
     * @param string $ending Ending to be appended to the trimmed string.
     * @param boolean $exact If false, $text will not be cut mid-word
     * @param boolean $considerHtml If true, HTML tags would be handled correctly
     * @return string Trimmed string.
     */
    public static function truncate($text, $length = 100, $ending = '...', $exact = true, $considerHtml = false)
    {
        if ($considerHtml) {
            // if the plain text is shorter than the maximum length, return the whole text
            if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
                return $text;
            }
            // splits all html-tags to scanable lines
            preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
            $total_length = strlen($ending);
            $open_tags = array();
            $truncate = '';
            foreach ($lines as $line_matchings) {
                // if there is any html-tag in this line, handle it and add it (uncounted) to the output
                if (!empty($line_matchings[1])) {
                    // if it's an "empty element" with or without xhtml-conform closing slash (f.e. <br/>)
                    if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
                        // do nothing
                        // if tag is a closing tag (f.e. </b>)
                    } else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
                        // delete tag from $open_tags list
                        $pos = array_search($tag_matchings[1], $open_tags);
                        if ($pos !== false) {
                            unset($open_tags[$pos]);
                        }
                        // if tag is an opening tag (f.e. <b>)
                    } else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
                        // add tag to the beginning of $open_tags list
                        array_unshift($open_tags, strtolower($tag_matchings[1]));
                    }
                    // add html-tag to $truncate'd text
                    $truncate .= $line_matchings[1];
                }
                // calculate the length of the plain text part of the line; handle entities as one character
                $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
                if ($total_length + $content_length > $length) {
                    // the number of characters which are left
                    $left = $length - $total_length;
                    $entities_length = 0;
                    // search for html entities
                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                        // calculate the real length of all entities in the legal range
                        foreach ($entities[0] as $entity) {
                            if ($entity[1] + 1 - $entities_length <= $left) {
                                $left--;
                                $entities_length += strlen($entity[0]);
                            } else {
                                // no more characters left
                                break;
                            }
                        }
                    }
                    $truncate .= substr($line_matchings[2], 0, $left + $entities_length);
                    // maximum lenght is reached, so get off the loop
                    break;
                } else {
                    $truncate .= $line_matchings[2];
                    $total_length += $content_length;
                }
                // if the maximum length is reached, get off the loop
                if ($total_length >= $length) {
                    break;
                }
            }
        } else {
            if (strlen($text) <= $length) {
                return $text;
            } else {
                $truncate = substr($text, 0, $length - strlen($ending));
            }
        }
        // if the words shouldn't be cut in the middle...
        if (!$exact) {
            // ...search the last occurance of a space...
            $spacepos = strrpos($truncate, ' ');
            if (isset($spacepos)) {
                // ...and cut the text in this position
                $truncate = substr($truncate, 0, $spacepos);
            }
        }
        // add the defined ending to the text
        $truncate .= $ending;
        if ($considerHtml) {
            // close all unclosed html-tags
            foreach ($open_tags as $tag) {
                $truncate .= '</' . $tag . '>';
            }
        }
        return $truncate;
    }

    public static function longword_break($str, $word_width)
    {
        $new_str = '';
        $len = strlen($str);
        $temp_str = '';
        $seperator = ' ';
        $is_opened = false;
        for ($i = 0; $i < $len; $i++) {
            //open
            if ($str[$i] == '<') {
                $is_opened = true;
                $new_str .= wordwrap($temp_str, $word_width, $seperator, 1);;
                $temp_str = '';
                $new_str .= '<';
                //close
            } else if ($str[$i] == '>') {
                $is_opened = false;
                $new_str .= '>';
            } else {
                if ($is_opened) {
                    $new_str .= $str[$i];
                } else {
                    if ($str[$i] == $seperator) {
                        $new_str .= $temp_str . $seperator;
                        $temp_str = '';
                    } else {
                        $temp_str .= $str[$i];
                    }
                }
            }
        }
        return $new_str;
    }


    public static function longword_break_old($str, $i)
    {

        if ((empty($str)) || (strlen($str) < $i)) {
            return $str;
        }
        $words_arr = explode(' ', $str);
        foreach ($words_arr as &$word) {
            $br_word = explode("\n", $word);
            //Process some run-in words conected by "\n"
            foreach ($br_word as &$r_word) {
                $j = $i;

                while ($j < strlen($r_word)) {

                    $r_word = substr($r_word, 0, $j) . ' ' . substr($r_word, $j);

                    $j += ($i + 1);
                }
            }
            $word = implode("\n", $br_word);
            unset($r_word);
        }
        unset($word);
        $str = implode(' ', $words_arr);


        //return  nl2br(wordwrap($str, $i,"\n", true));
        //return  nl2br($str);
        return $str;

    }

    public static function generateSiteMap()
    {
        $articles = ArticlePeer::doSelect(new Criteria());

        $sitemap = new sfSitemap();
        $sitemap->setFileName("sitemap.xml.gz");
        $sitemap->setOutputType("gzip");
        $sitemap->openFile();

        foreach ($articles as $article) {
            // save siteMap
            $item = new sfSitemapItem();
            $item->setLoc('@article?stripped_title=' . $article->getStrippedTitle());
            $item->setLastMod(time());
            $item->setChangeFreq('monthly');
            $sitemap->addItem($item);
        }

        // close sitemap file
        $sitemap->closeFile();
    }

    public static function cp1251_utf8($word)
    {
        $cyr_lower_chars = array(
            'е', 'щ', 'ф', 'ц', 'у', 'ж', 'э',
            'н', 'г', 'ш', 'ү', 'з', 'к', 'ъ',
            'й', 'ы', 'б', 'ө', 'а', 'х', 'р',
            'о', 'л', 'д', 'п', 'я', 'ч', 'ё',
            'с', 'м', 'и', 'т', 'ь', 'в', 'ю',);

        $latin_lower_chars = array(
            'å', 'ù', 'ô', 'ö', 'ó', 'æ', 'ý',
            'í', 'ã', 'ø', '¿', 'ç', 'ê', 'ú',
            'é', 'û', 'á', 'º', 'à', 'õ', 'ð',
            'î', 'ë', 'ä', 'ï', 'ÿ', '÷', '¸',
            'ñ', 'ì', 'è', 'ò', 'ü', 'â', 'þ',);

        $cyr_upper_chars = array(
            'Е', 'Щ', 'Ф', 'Ц', 'У', 'Ж', 'Э',
            'Н', 'Г', 'Ш', 'Ү', 'З', 'К', 'Ъ',
            'Й', 'Ы', 'Б', 'Ө', 'А', 'Х', 'Р',
            'О', 'Л', 'Д', 'П', 'Я', 'Ч', 'Ё',
            'С', 'М', 'И', 'Т', 'Ь', 'В', 'Ю',);

        $latin_upper_chars = array(
            'Å', 'Ù', 'Ô', 'Ö', 'Ó', 'Æ', 'Ý',
            'Í', 'Ã', 'Ø', '¯', 'Ç', 'Ê', 'Ú',
            'É', 'Û', 'Á', 'ª', 'À', 'Õ', 'Ð',
            'Î', 'Ë', 'Ä', 'Ï', 'ß', '×', '¨',
            'Ñ', 'Ì', 'È', 'Ò', 'Ü', 'Â', 'Þ',);

        //replacing lower cyrillic
        $word = str_replace($latin_lower_chars, $cyr_lower_chars, $word);
        //replacing upper cyrillic
        $word = str_replace($latin_upper_chars, $cyr_upper_chars, $word);
        return $word;
    }

    public static function date($str)
    {
        $str = self::cp1251_utf8($str);
        $date_arr = explode('-', $str);
        if (count($date_arr) != 3) return '';
        $year = $date_arr[0];
        $month = $date_arr[1];
        $day = $date_arr[2];
        if (self::mb_strlen($year) != 4 && is_numeric($year)) return '';
        if (self::mb_strlen($month) != 2 && is_numeric($month)) return '';
        if (self::mb_strlen($day) != 2 && is_numeric($day)) return '';

        return $str;
    }

    public static function get_query_string()
    {
        $qry_str = array();
        $query = $_SERVER['QUERY_STRING'];
        $query_arr = $query ? explode('&', $query) : array();
        foreach ($query_arr as $qry) {
            $param = explode('=', $qry);
            $value = $param[1];
            if (trim($value)) {
                $qry_str[] = $qry;
            }
        }

        return join('&', $qry_str);
    }

    public static function getFileMimeType($v)
    {
        $types = array(
            "ez" => "application/andrew-inset",
            "hqx" => "application/mac-binhex40",
            "cpt" => "application/mac-compactpro",
            "doc" => "application/msword",
            "bin" => "application/octet-stream",
            "dms" => "application/octet-stream",
            "lha" => "application/octet-stream",
            "lzh" => "application/octet-stream",
            "exe" => "application/octet-stream",
            "class" => "application/octet-stream",
            "so" => "application/octet-stream",
            "dll" => "application/octet-stream",
            "oda" => "application/oda",
            "pdf" => "application/pdf",
            "ai" => "application/postscript",
            "eps" => "application/postscript",
            "ps" => "application/postscript",
            "smi" => "application/smil",
            "smil" => "application/smil",
            "wbxml" => "application/vnd.wap.wbxml",
            "wmlc" => "application/vnd.wap.wmlc",
            "wmlsc" => "application/vnd.wap.wmlscriptc",
            "bcpio" => "application/x-bcpio",
            "vcd" => "application/x-cdlink",
            "pgn" => "application/x-chess-pgn",
            "cpio" => "application/x-cpio",
            "csh" => "application/x-csh",
            "dcr" => "application/x-director",
            "dir" => "application/x-director",
            "dxr" => "application/x-director",
            "dvi" => "application/x-dvi",
            "spl" => "application/x-futuresplash",
            "gtar" => "application/x-gtar",
            "hdf" => "application/x-hdf",
            "js" => "application/x-javascript",
            "skp" => "application/x-koan",
            "skd" => "application/x-koan",
            "skt" => "application/x-koan",
            "skm" => "application/x-koan",
            "latex" => "application/x-latex",
            "nc" => "application/x-netcdf",
            "cdf" => "application/x-netcdf",
            "sh" => "application/x-sh",
            "shar" => "application/x-shar",
            "swf" => "application/x-shockwave-flash",
            "sit" => "application/x-stuffit",
            "sv4cpio" => "application/x-sv4cpio",
            "sv4crc" => "application/x-sv4crc",
            "tar" => "application/x-tar",
            "tcl" => "application/x-tcl",
            "tex" => "application/x-tex",
            "texinfo" => "application/x-texinfo",
            "texi" => "application/x-texinfo",
            "t" => "application/x-troff",
            "tr" => "application/x-troff",
            "roff" => "application/x-troff",
            "man" => "application/x-troff-man",
            "me" => "application/x-troff-me",
            "ms" => "application/x-troff-ms",
            "ustar" => "application/x-ustar",
            "src" => "application/x-wais-source",
            "xhtml" => "application/xhtml+xml",
            "xht" => "application/xhtml+xml",
            "zip" => "application/zip",
            "au" => "audio/basic",
            "snd" => "audio/basic",
            "mid" => "audio/midi",
            "midi" => "audio/midi",
            "kar" => "audio/midi",
            "mpga" => "audio/mpeg",
            "mp2" => "audio/mpeg",
            "mp3" => "audio/mpeg",
            "aif" => "audio/x-aiff",
            "aiff" => "audio/x-aiff",
            "aifc" => "audio/x-aiff",
            "m3u" => "audio/x-mpegurl",
            "ram" => "audio/x-pn-realaudio",
            "rm" => "audio/x-pn-realaudio",
            "rpm" => "audio/x-pn-realaudio-plugin",
            "ra" => "audio/x-realaudio",
            "wav" => "audio/x-wav",
            "pdb" => "chemical/x-pdb",
            "xyz" => "chemical/x-xyz",
            "bmp" => "image/bmp",
            "gif" => "image/gif",
            "ief" => "image/ief",
            "jpeg" => "image/jpeg",
            "jpg" => "image/jpeg",
            "jpe" => "image/jpeg",
            "png" => "image/png",
            "tiff" => "image/tiff",
            "tif" => "image/tif",
            "djvu" => "image/vnd.djvu",
            "djv" => "image/vnd.djvu",
            "wbmp" => "image/vnd.wap.wbmp",
            "ras" => "image/x-cmu-raster",
            "pnm" => "image/x-portable-anymap",
            "pbm" => "image/x-portable-bitmap",
            "pgm" => "image/x-portable-graymap",
            "ppm" => "image/x-portable-pixmap",
            "rgb" => "image/x-rgb",
            "xbm" => "image/x-xbitmap",
            "xpm" => "image/x-xpixmap",
            "xwd" => "image/x-windowdump",
            "igs" => "model/iges",
            "iges" => "model/iges",
            "msh" => "model/mesh",
            "mesh" => "model/mesh",
            "silo" => "model/mesh",
            "wrl" => "model/vrml",
            "vrml" => "model/vrml",
            "css" => "text/css",
            "html" => "text/html",
            "htm" => "text/html",
            "asc" => "text/plain",
            "txt" => "text/plain",
            "rtx" => "text/richtext",
            "rtf" => "text/rtf",
            "sgml" => "text/sgml",
            "sgm" => "text/sgml",
            "tsv" => "text/tab-seperated-values",
            "wml" => "text/vnd.wap.wml",
            "wmls" => "text/vnd.wap.wmlscript",
            "etx" => "text/x-setext",
            "xml" => "text/xml",
            "xsl" => "text/xml",
            "mpeg" => "video/mpeg",
            "mpg" => "video/mpeg",
            "mpe" => "video/mpeg",
            "qt" => "video/quicktime",
            "mov" => "video/quicktime",
            "mxu" => "video/vnd.mpegurl",
            "avi" => "video/x-msvideo",
            "movie" => "video/x-sgi-movie",
            "ice" => "x-conference-xcooltalk",
            "xls" => "application/vnd.ms-excel"
        );

        return array_key_exists($v, $types) ? $types[$v] : 'application/octet-stream';
    }

    public static function read_xml($src, $table, $field_type, $field_email)
    {
        $return = array();
        // reading
        $doc = new DOMDocument();
        $doc->load($src);

        $items = $doc->getElementsByTagName($table);
        foreach ($items as $item) {
            $values = $item->getElementsByTagName($field_type);
            $name = strip_tags(trim(self::cp1251_utf8($values->item(0)->nodeValue)));
            unset($values);

            $values = $item->getElementsByTagName($field_email);
            $email = trim($values->item(0)->nodeValue);
            unset($values);

            $return[] = array($name => $email);
        }

        return $return;
    }

    public static function write_xls($filename, $headers, $values, $sizes = array())
    {
        // include package
        error_reporting(E_ALL ^ E_NOTICE);
        require_once 'Spreadsheet/Excel/Writer.php';
        $workbook = new Spreadsheet_Excel_Writer();
        $workbook->setVersion(8);

        $format_header =& $workbook->addFormat();
        $format_header->setColor('white');
        $format_header->setPattern(1);
        $format_header->setFgColor('grey');
        $format_header->setBorder(1);
        $format_header->setAlign('right');

        $format_body =& $workbook->addFormat();
        $format_body->setBorder(1);
        $format_body->setAlign('right');

        $worksheet =& $workbook->addWorksheet();
        $worksheet->setInputEncoding('utf-8');
        if (!empty($sizes)) {
            for ($i = 0; $i < sizeof($sizes); $i++) {
                $worksheet->setColumn($i, $i, $sizes[$i]);
            }
        }
        foreach ($headers as $key => $header) {
            $worksheet->write(0, $key, $header, $format_header);
        }

        $rowCount = 1;
        foreach ($values as $row) {
            foreach ($row as $key => $value) {
                $worksheet->write($rowCount, $key, $value, $format_body);
            }
            $rowCount++;
        }

        $workbook->send($filename . '.xls');
        $workbook->close();
    }

    public static function toString($v)
    {
        return self::cp1251_utf8(trim($v));
    }
    
    public static function formatCurrency($price, $unit = 'MNT')
    {
    if ($unit == "MNT")
    {
      return number_format($price, 0, '.', ','). '₮';
    }
    else
    {
      return number_format($price, 0, '.', ','). '$';
    }
  }
}

?>