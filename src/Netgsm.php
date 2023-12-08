<?php

namespace Fatihozpolat\Netgsm;

use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Netgsm
{
    protected Client $client;

    protected string $username;

    protected string $password;

    protected string $header;

    protected string $language;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('netgsm.url'),
            'verify' => false,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        $this->username = config('netgsm.username');
        $this->password = config('netgsm.password');
        $this->header = config('netgsm.header');
        $this->language = config('netgsm.language');
    }

    /**
     * Kuyruğa yeni numara eklemek için kullanılır.
     *
     * @param  string  $queue Santralinizde tanımlı olan kuyruk(departman) bilgisi. (85030XXXXX-queue-kuyrukismi formatında gönderilmeli) Zorunlu parametre
     * @param  string|array  $no Kuyruğa eklenecek ya da kuruktan çıkarılacak numara ya da numaralar. 5xxxxxxxxx, 312xxxxxxx formatında gönderilmeli. Zorunlu parametre
     *
     * @throws GuzzleException
     */
    public function queueAdd(string $queue, string|array $no): array
    {
        return $this->queue(config('netgsm.tenant'), $queue, $no);
    }

    /**
     * Kuyruktan numara çıkarmak için kullanılır.
     *
     * @param  string  $queue Santralinizde tanımlı olan kuyruk(departman) bilgisi. (85030XXXXX-queue-kuyrukismi formatında gönderilmeli) Zorunlu parametre
     * @param  string|array  $no Kuyruğa eklenecek ya da kuruktan çıkarılacak numara. 5xxxxxxxxx, 312xxxxxxx formatında gönderilmeli. Zorunlu parametre
     *
     * @throws GuzzleException
     */
    public function queueDel(string $queue, string|array $no): array
    {
        return $this->queue(config('netgsm.tenant'), $queue, $no, 'queuedelnumber');
    }

    /**
     * Netsantral'iniz üzerinde kuyruğa harici (dahili dışında) numara/ekleyip çıkarma işlemini API ile de sağlayabilirsiniz. Gelen çağrıları karşılamasını istediğiniz, belirleyeceğiniz kuyruğa (departmana) harici numara eklemek ya da çıkarmak için, servise JSON post edebilirsiniz. Alacağınız dönüş JSON formatında olacaktır.
     *
     * @link https://www.netgsm.com.tr/netsantraldokuman/#kuyru%C4%9Fa-d%C4%B1%C5%9F-numara-ekleme%C3%A7%C4%B1karma Kuyruğa Dış Numara Ekleme/Çıkarma
     *
     * @param  string  $tenant Santral numarası. Zorunlu parametre
     * @param  string  $queue Santralinizde tanımlı olan kuyruk(departman) bilgisi. (85030XXXXX-queue-kuyrukismi formatında gönderilmeli) Zorunlu parametre
     * @param  string|array  $no Kuyruğa eklenecek ya da kuruktan çıkarılacak numara ya da numalar. 5xxxxxxxxx, 312xxxxxxx formatında gönderilmeli. Zorunlu parametre
     * @param  string  $command Numara ekleme işlemi için queueaddnumber , listeden numara çıkarmak için queuedelnumber gönderebilirsiniz. Zorunlu parametre
     * @param  int  $penalty Çağrıların numaralara dağıtımı sırasındaki önceliği belirler.Düşük değerler yüksek önceliğe sahiptir.Birden fazla numara aynı önceliğe sahip olabilir.Dahili numaralarda ve harici numaralarda belirlemiş olduğunuz öncelikler birbirini etkilemektedir. 1-10 arası değer alabilir. Zorunlu parametre
     *
     * @throws GuzzleException
     */
    private function queue(string $tenant, string $queue, string|array $no, string $command = 'queueaddnumber', int $penalty = 1): array
    {
        $payload = [
            'username' => $this->username,
            'password' => $this->password,
            'command' => $command,
            'tenant' => $tenant,
            'queue' => $queue,
        ];

        if (is_array($no)) {
            $payload['numbers'] = collect($no)->map(function ($item) use ($penalty) {
                if (is_array($item) && ! isset($item['no'])) {
                    throw new Exception('Netgsm Queue Error: no param is required');
                }

                return [
                    'no' => $item['no'] ?? $item,
                    'penalty' => $item['penalty'] ?? $penalty,
                ];
            })->toArray();
        } else {
            $payload['no'] = $no;
            $payload['penalty'] = $penalty;
        }

        $res = $this->client->post('netsantral/queue', [
            'json' => $payload,
        ]);

        $res = $res->getBody()->getContents();

        return json_decode($res, true);
    }

    /**
     * Verilen Tenant ve Queue bilgilerine göre, kuyruktaki numaraların durumlarını sorgulayabilirsiniz.
     *
     * @link https://www.netgsm.com.tr/netsantraldokuman/#kuyruk-durum-sorgulama Kuyruk Durum Sorgulama
     *
     * @throws GuzzleException
     */
    public function queueStats(string $tenant, string $queue): array
    {
        $customClient = new Client([
            'base_uri' => 'http://crmsntrl.netgsm.com.tr:9111/',
            'verify' => false,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        $res = $customClient->get($tenant.'/'.'queuestats', [
            'query' => [
                'username' => $this->username,
                'password' => $this->password,
                'queue' => $queue,
                'crm_id' => 1,
            ],
        ]);

        $res = $res->getBody()->getContents();

        return json_decode($res, true);
    }

    /**
     * SMS gönderimi için kullanılır.
     *
     * @link https://www.netgsm.com.tr/dokuman/#http-post-sms-g%C3%B6nderme HTTP Post ile SMS Gönderme
     *
     * @param  mixed  $phones Dizi ya da string olarak gönderilebilir. başında 0 olmadan 10 haneli olmalıdır.
     * @param  string  $message SMS içeriği
     *
     * @throws GuzzleException
     */
    public function sendSms(mixed $phones, string $message): array
    {
        $phones = is_array($phones) ? implode(',', $phones) : $phones;
        $phones = str_replace('+90', '', $phones);

        $res = $this->client->get('sms/send/get', [
            'query' => [
                'usercode' => $this->username,
                'password' => $this->password,
                'gsmno' => $phones,
                'message' => $message,
                'msgheader' => $this->header,
                'dil' => $this->language,
            ],
        ]);

        $res = $res->getBody()->getContents();

        return explode(' ', $res);
    }

    /**
     * Gönderilen mesajların son 3 aya kadar raporlarını sorguyarak; iletim durumlarını öğrenebilirsiniz.
     *
     * @link https://www.netgsm.com.tr/dokuman/#http-get-rapor #HTTP Get ile Rapor Sorgulama
     *
     * @throws GuzzleException
     * @throws Exception
     */
    public function report(mixed $bulkId, int $status = 100, int $version = 2): array
    {
        $type = 0;
        if (is_array($bulkId)) {
            $bulkId = implode(',', $bulkId);
            $type = 1;
        }

        $res = $this->client->get('sms/report', [
            'query' => [
                'usercode' => $this->username,
                'password' => $this->password,
                'bulkid' => $bulkId,
                'type' => $type,
                'status' => $status,
                'version' => $version,
            ],
        ]);

        $res = $res->getBody()->getContents();

        $res = rtrim($res, '<br>');
        $data = explode(' ', $res);

        if (count($data) !== 7) {
            throw new Exception('Netgsm Report Error: '.$res);
        }

        return [
            'phone' => ltrim($data[0], '90'),
            'status' => $this->smsStatus($data[1]),
            'operator' => $this->smsOperator($data[2]),
            'message_length' => $data[3],
            'sent_at' => Carbon::parse($data[4].' '.$data[5])->toDateTimeString(),
            'error' => $this->smsError($data[6]),
        ];
    }

    private function smsStatus($status): string
    {
        return match ($status) {
            '0' => 'İletilmeyi bekleyenler',
            '1' => 'İletilmiş',
            '2' => 'Zaman aşımına uğramış',
            '3' => 'Hatalı veya kısıtlı numara',
            '4' => 'Operatöre gönderilemedi',
            '11' => 'Operatör tarafından kabul edilmemiş',
            '12' => 'Gönderim hatası',
            '13' => 'Mükerrer',
            '100' => 'Tüm mesaj durumları',
            '103' => 'Başarısız Görev (Bu görevin tamamı başarısız olmuştur.)',
            default => 'Bilinmiyor',
        };
    }

    private function smsOperator($code): string
    {
        return match ($code) {
            '10' => 'Vodafone',
            '20' => 'Türk Telekom',
            '30' => 'Turkcell',
            '40' => 'Netgsm STH',
            '41' => 'Netgsm Mobil',
            '160' => 'KKTC Vodafone',
            '214', '213', '215', '212' => 'Yurtdışı',
            '880' => 'KKTC Turkcell',
            default => 'Bilinmiyor',
        };
    }

    private function smsError($code): string
    {
        return match ($code) {
            '0' => 'Hata Yok',
            '101' => 'Mesaj Kutusu Dolu',
            '102' => 'Kapalı yada Kapsama Dışında',
            '103' => 'Meşgul',
            '104' => 'Hat Aktif Değil',
            '105' => 'Hatalı Numara',
            '106' => 'SMS red, Karaliste',
            '111' => 'Zaman Aşımı',
            '112' => 'Mobil Cihaz Sms Gönderimine Kapalı',
            '113' => 'Mobil Cihaz Desteklemiyor',
            '114' => 'Yönlendirme Başarısız',
            '115' => 'Çağrı Yasaklandı',
            '116' => 'Tanımlanamayan Abone',
            '117' => 'Yasadışı Abone',
            '119' => 'Sistemsel Hata',
            default => 'Bilinmiyor',
        };
    }
}
