<?
\Bitrix\Main\Loader::includeModule('highloadblock');
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

class SeoOverride
{
    private $uri;
    private $title;
    private $description;
    private $keywords;
    private $options;

    public function __construct($uri)
    {
        $this->setUri($uri);
        $element = $this->getIblockElement();

        if (!empty($element['PROPERTY_META_TITLE_VALUE'])) {
            $this->setTitle($element['PROPERTY_META_TITLE_VALUE']);
        } else {
            $this->setTitle('');
        }

        if (!empty($element['PROPERTY_META_DESCRIPTION_VALUE'])) {
            $this->setDescription($element['PROPERTY_META_DESCRIPTION_VALUE']);
        } else {
            $this->setDescription('');
        }

        if (!empty($element['PROPERTY_META_KEYWORDS_VALUE'])) {
            $this->setKeywords($element['PROPERTY_META_KEYWORDS_VALUE']);
        } else {
            $this->setKeywords('');
        }
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getKeywords()
    {
        return $this->keywords;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setUri(string $value)
    {
        $this->uri = $value;
        return $this;
    }

    public function setTitle(string $value)
    {
        $this->title = $value;
        return $this;
    }

    public function setDescription(string $value)
    {
        $this->description = $value;
        return $this;
    }

    public function setKeywords(string $value)
    {
        $this->keywords = $value;
        return $this;
    }

    public function setOptions(array $value)
    {
        $this->options = $value;
        return $this;
    }

    private function getIblockElement()
    {
        $result = false;

        $arOrder = ['SORT' => 'ASC'];
        $arFilter = [
            'IBLOCK_ID' => CLocals::IBLOCK_ID_SEO_OVERRIDE, 
            'ACTIVE' => 'Y', 
            'CODE' => $this->getUri()
        ];
        $arSelect = [
            'IBLOCK_ID', 
            'ID', 
            'CODE', 
            'PROPERTY_META_TITLE', 
            'PROPERTY_META_DESCRIPTION', 
            'PROPERTY_META_KEYWORDS'
        ];

        $resElements = CIBlockElement::GetList(
            $arOrder, 
            $arFilter, 
            false, 
            false, 
            $arSelect
        );

        while($arElement = $resElements->GetNext())
        {
            $result = $arElement;
        }

        return $result;
    }

    public function setMetaTags()
    {
        if (!empty($this->title)) {
            $this->setMetaTitle();
        }

        if (!empty($this->description)) {
            $this->setMetaDescription();
        }

        if (!empty($this->keywords)) {
            $this->setMetaKeywords();
        }
    }

    private function setMetaTitle()
    {
        global $APPLICATION;

        $APPLICATION->SetTitle($this->getTitle(), $this->getOptions());
        
        $APPLICATION->SetPageProperty(
            "title", 
            $this->getTitle(), 
            $this->getOptions()
        );
    }

    private function setMetaDescription()
    {
        global $APPLICATION;

        $APPLICATION->SetPageProperty(
            "description", 
            $this->getDescription(), 
            $this->getOptions()
        );
    }

    private function setMetaKeywords()
    {
        global $APPLICATION;

        $APPLICATION->SetPageProperty(
            "keywords", 
            $this->getKeywords(), 
            $this->getOptions()
        );
    }
}