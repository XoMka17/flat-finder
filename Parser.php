<?php

include('simplehtmldom_1_9_1/simple_html_dom.php');

class Parser
{

    private $dbPath = '/var/www/html/home/db/';
    private $olxFile = 'olx';
    private $realtyFile = 'realty';

    private $olxLink = 'https://www.olx.ua/uk/nedvizhimost/kvartiry-komnaty/arenda-kvartir-komnat/kvartira/kiev/?search%5Bfilter_float_price%3Ato%5D=7500&search%5Bdistrict_id%5D=15';
    private $realtyLink = 'https://100realty.ua/uk/realty_search/apartment/rent/m_201326597%2C201326596%2C31/nr_1%2C2/p_0_7500/cur_3/kch_2/sort/id_desc#realty-search-sort';
    private $domRiaLink = 'https://dom.ria.com/ru/search/#links-under-filter=on&category=1&realty_type=2&operation_type=3&fullCategoryOperation=1_2_3&page=0&state_id=10&city_id=10&limit=20&sort=inspected_sort&period=per_allday&csrf=oOUrSdWp-8v9LupBKQNycg24G89_0kfbU-0I&m_id=19:20:21&ch=235_t_7500,246_244';

    private $olxInfo;
    private $realtyInfo;
    private $domRiaInfo;


    private $curl;

    /**
     * @return mixed
     */
    public function getOlxInfo()
    {
        return $this->olxInfo;
    }

    /**
     * @return mixed
     */
    public function getRealtyInfo()
    {
        return $this->realtyInfo;
    }

    /**
     * @return mixed
     */
    public function getDomRiaInfo()
    {
        return $this->domRiaInfo;
    }

    public function __construct()
    {
        $this->dbPath = dirname(__FILE__) . '/db/';

        $this->olxFile = $this->dbPath . $this->olxFile;
        $this->realtyFile = $this->dbPath . $this->realtyFile;

        $this->olxInfo = '';
        $this->realtyInfo = '';

        $this->curlInit();
    }

    public function __destruct()
    {
        curl_close($this->curl);
    }

    public function curlInit() {

        if(file_exists(dirname(__FILE__) . '/cookie.txt'))
            unlink(dirname(__FILE__) . '/cookie.txt');

//        $proxy_ip = '185.93.3.123';
//        $proxy_port = 8080;

        $this->curl = curl_init();
        curl_setopt($this->curl,CURLOPT_HEADER,1);
        curl_setopt($this->curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($this->curl,CURLOPT_FOLLOWLOCATION,true);
        curl_setopt($this->curl,CURLOPT_CONNECTTIMEOUT,30);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->curl,CURLOPT_USERAGENT,'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.138 Safari/537.36');
        curl_setopt($this->curl,CURLOPT_COOKIEJAR,dirname(__FILE__) . '/cookie.txt');
        curl_setopt($this->curl,CURLOPT_COOKIEFILE,dirname(__FILE__) . '/cookie.txt');

        curl_setopt($this->curl, CURLOPT_HTTPHEADER, [
            'cache-control: no-store, no-cache, must-revalidate',
            'content-encoding: gzip',
            'content-type: text/html; charset=UTF-8',
            'date: Sat, 06 Jun 2020 08:41:37 GMT',
            'expires: Thu, 19 Nov 1981 08:52:00 GMT',
            'referrer-policy: unsafe-url',
            'server: OLXcdn',

            'status: 200',
            'strict-transport-security: max-age=31536000; includeSubDomains',
            'vary: Accept-Encoding',
            'via: 1.1 1008748c844980a7bf932624d793da48.cloudfront.net (CloudFront)',
            'x-amz-cf-id: hpe0xHTTBcf9LUquz7cojafeJ4KWEAEC9C_gufEBrFiPQRFFAcyllQ==',
            'x-amz-cf-pop: BUD50-C1',
            'x-cache: Miss from cloudfront',
            'x-content-type-options: nosniff',
            'x-request-processing-time: D=298231',
            'x-request-received: t=1591432897696834',
            'x-t: True',
            'x-xss-protection: 1',


            'sec-fetch-dest: document',
            'sec-fetch-mode: navigate',
            'sec-fetch-site: same-origin',
            'sec-fetch-user: ?1',
            'upgrade-insecure-requests: 1',

            'accept-language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7',
            'cache-control: max-age=0'
        ]);

    }

    public function curlGetPage($url) {
        curl_setopt($this->curl,CURLOPT_URL,$url);
        curl_setopt($this->curl,CURLOPT_REFERER,$url);

        return curl_exec($this->curl);
    }


    public function find() {
        $this->olx();
        $this->realty();
//        $this->domRia();
    }

    public function olx() {
        $last = file_get_contents($this->olxFile);

//        $page = file_get_html($this->olxLink);
        $page = str_get_html($this->curlGetPage($this->olxLink));

        $list = $page->find('table[id=offers_table]',0);

        foreach ($list->find('tr[class=wrap]') as $item ) {
            $link = $this->clearLink($item->find('td[class=title-cell] a',0)->href);

            if(strlen($last) > 0) {
                if(strpos($last,$link) !== false || strpos($link,$last) !== false ) {
                    break;
                }
            }
// . '<br><hr></tr>'
            $this->olxInfo .= $item . '<tr><td>' . $this->makeLinkForSave('olx',$link) . '</td></tr>';
        }
    }

    public function realty() {
        $last = file_get_contents($this->realtyFile);

        $page = file_get_html($this->realtyLink);

        $list = $page->find('div[id=realty-search-results]',0);

        foreach ($list->find('div[class=realty-object-card  odd], div[class=realty-object-card  even]') as $item ) {
            $link = $this->clearLink($item->find('div[class=object-address] a',0)->href);

            if(strlen($last) > 0) {
                if(strpos($last,$link) !== false || strpos($link,$last) !== false ) {
                    break;
                }
            }

            $item = preg_replace(
                ['|' . $link . '|','|src="/sites/100realty.ua/files/image-preloader.gif"|','|data-lazy|','<img src="/sites/all/modules/_custom/favorite_objects/images/star_fade.png" alt="star_fade" typeof="foaf:Image">'],
                ['https://100realty.ua' . $link,'','src',''],
                $item);


            $this->realtyInfo .= $item .  $this->makeLinkForSave('realty',$link) . '<br><hr>';
        }
    }

    public function domRia() {
        $page = file_get_html($this->domRiaLink);

//        echo $page;
        $list = $page->find('div[id=searchResults]',0);

//        echo $list;

        foreach ($list->find('div[class=ticket-clear line]') as $item ) {
            $this->domRiaInfo .= $item . '<br><hr>';
            echo $item;
        }
    }

    public function clearLink($link) {
        return preg_replace('|#(.*)|','',$link);
    }



    public function makeLinkForSave($file,$link) {
        return '<a href="api/index.php?do=save&file=' . $file .'&link=' . $link . '">Отметить как просмотренное</a>';
    }
}