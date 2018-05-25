<?php
/**
 * Created by PhpStorm.
 * User: gustavoweb
 * Date: 14/12/2017
 * Time: 16:27
 */

namespace Model;

class Calendar
{

    private $client;
    private $trigger;
    private $event;
    private $params;
    private $service;

    /**
     * Calendar constructor.
     * Método responsável por inicializar a comunicação com a API do Google
     * @throws \Google_Exception
     */
    public function __construct()
    {
        define('APPLICATION_NAME', 'UpInside');
        define('CREDENTIALS_PATH', '~/.credentials/calendar-php-quickstart.json');
        define('CLIENT_SECRET_PATH', __DIR__ . '/../../client_secret.json');
        define('SCOPES', implode(' ', [\Google_Service_Calendar::CALENDAR]));

        $this->client = new \Google_Client();
        $this->client->setApplicationName(APPLICATION_NAME);
        $this->client->setScopes(SCOPES);
        $this->client->setAuthConfig(CLIENT_SECRET_PATH);
        $this->client->setAccessType('offline');
    }

    /**
     * <b>getTrigger:</b> Método responsável por retornar mensagem de erro
     * @return mixed
     */
    public function getTrigger()
    {
        return $this->trigger;
    }

    /**
     * <b>createClient:</b> Método responsável por gerar o link de autenticação da API
     * @return string
     */
    public function createClient()
    {
        $authUrl = $this->client->createAuthUrl();
        return $authUrl;
    }

    /**
     * <b>setAccessToken:</b> Método responsável por criar o credenciamento da API,
     * salvar o arquivo .json no diretório especificado da define
     * @param $authCode
     * @return bool
     */
    public function setAccessToken($authCode)
    {
        $accessToken = $this->client->fetchAccessTokenWithAuthCode($authCode);

        // Retorna o caminho absoluto
        $credentialsPath = $this->expandHomeDirectory(CREDENTIALS_PATH);

        // Cria o diretório de forma recursiva para armazenar o .json
        if (!file_exists(dirname($credentialsPath))) {
            mkdir(dirname($credentialsPath), 0700, true);
        }

        // Salva o credenciamento dentro da pasta
        file_put_contents($credentialsPath, json_encode($accessToken));
        return true;
    }

    /**
     * <b>getClient:</b> Método responsável por obter o client do Google
     * @return bool|\Google_Client
     */
    public function getClient()
    {
        // Load previously authorized credentials from a file.
        $credentialsPath = $this->expandHomeDirectory(CREDENTIALS_PATH);
        if (!file_exists($credentialsPath)) {
            $this->trigger = 'Não há credenciais definidas!';
            return false;
        } else {
            $accessToken = json_decode(file_get_contents($credentialsPath), true);
            $this->client->setAccessToken($accessToken);
        }

        // Refresh the token if it's expired.
        if ($this->client->isAccessTokenExpired()) {
            $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            file_put_contents($credentialsPath, json_encode($this->client->getAccessToken()));
        }
        return $this->client;
    }

    /**
     * <b>createEvent:</b> Método responsável por criar o evento dentro do Google Calendar
     * @param STRING $summary = Título do Evento
     * @param STRING $location = Endereço completo do onde ocorrerá o evento
     * @param STRING $description = Descrição do evento
     * @param DATETIME $start = Data e Hora no formato americano
     * @param DATETIME $end = Data e Hora no formato americano
     * @param null|STRING $attendees = E-mail do Convidado
     * @return bool|\Google_Service_Calendar_Event
     */
    public function createEvent($summary, $location, $description, $start, $end, $attendees = null)
    {
        if (date('Y-m-d H:i:s', strtotime($start)) < date('Y-m-d H:i:s')) {
            $this->trigger = "A data inicial é menor do que a data atual, por favor verifique e tente novamente!";
            return false;
        }

        if (date('Y-m-d H:i:s', strtotime($end)) < date('Y-m-d H:i:s', strtotime($start))) {
            $this->trigger = "A data final é menor que a data de início, por favor verifique e tente novamente!";
            return false;
        }

        $this->params = array(
            'summary' => $summary,
            'location' => $location,
            'description' => $description,
            'start' => array(
                'dateTime' => date(DATE_ISO8601, strtotime($start)),
                'timeZone' => 'America/Sao_Paulo',
            ),
            'end' => array(
                'dateTime' => date(DATE_ISO8601, strtotime($end)),
                'timeZone' => 'America/Sao_Paulo',
            ),
            'reminders' => array(
                'useDefault' => false,
                'overrides' => array(
                    array('method' => 'email', 'minutes' => 24 * 60),
                    array('method' => 'popup', 'minutes' => 10),
                ),
            ),
        );

        if (!empty($attendees)) {
            if (filter_var($attendees, FILTER_VALIDATE_EMAIL)) {
                $this->params += [
                    'attendees' => array(
                        array('email' => $attendees),
                    ),
                ];
            }
        }

        $this->event = new \Google_Service_Calendar_Event($this->params);
        $this->service = new \Google_Service_Calendar($this->client);
        $this->event = $this->service->events->insert('primary', $this->event, ['sendNotifications' => true]);
        return $this->event;
    }

    /**
     * <b>deleteEvent:</b> Método responsável por deletar um evento do Google Calendar
     * @param STRING $eventId = ID do evento do Google
     */
    public function deleteEvent($eventId)
    {
        $this->service = new \Google_Service_Calendar($this->client);
        $this->service->events->delete('primary', $eventId, ['sendNotifications' => true]);
    }

    /**
     * <b>expandHomeDirectory:</b> Método responsável por expandir o diretório e normalizar o caminho absoluto
     * @param STRING $path = Caminho que deseja ser verificado
     * @return mixed
     */
    private function expandHomeDirectory($path)
    {
        $homeDirectory = getenv('HOME');
        if (empty($homeDirectory)) {
            $homeDirectory = getenv('HOMEDRIVE') . getenv('HOMEPATH');
        }
        return str_replace('~', realpath($homeDirectory), $path);
    }

}