<?php

namespace Markocupic\EmployeeBundle;

use Contao\Environment;


class ReplaceInsertTags
{

    /**
     * @param $strTag
     * @return bool|string
     */
    public function replaceInsertTags($strTag)
    {
        if (preg_match('/vcard_download_url::([\d])/', $strTag, $match))
        {
            return Environment::get('request') . '?downloadVCard=true&amp;id=' . $match[1];
        }

        return false;
    }
}

