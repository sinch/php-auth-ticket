/**
 * Generator class for Sinch user tickets
 */
class SinchTicketGenerator
{
    /**
     * @var string
     */
    private $applicationKey;
 
    /**
     * @var string
     */
    private $applicationSecret;
 
    public function __construct($applicationKey, $applicationSecret)
    {
        $this->applicationKey = $applicationKey;
        $this->applicationSecret = $applicationSecret;
    }
 
    /**
     * @param string   $username  Unique identifier, used to make and receive calls
     * @param DateTime $createdAt Ticket start time
     * @param int      $expiresIn Expiration in seconds after createdAt
     *
     * @return string
     */
    public function generateTicket($username, DateTime $createdAt, $expiresIn)
    {
        $userTicket = [
            'identity' => [
                'type'      => 'username',
                'endpoint'  => $username,
            ],
            'expiresIn'         => $expiresIn,
            'applicationKey'    => $this->applicationKey,
            'created'           => $createdAt->format('c'),
        ];
        $userTicketJson = preg_replace('/\s+/', '', json_encode($userTicket));
 
        $userTicketBase64 = $this->base64Encode($userTicketJson);
 
        $digest = $this->createDigest($userTicketJson);
 
        $signature = $this->base64Encode($digest);
 
        $userTicketSigned = $userTicketBase64.':'.$signature;
 
        return $userTicketSigned;
    }
 
    /**
     * 
     *
     * @param $data
     *
     * @return string
     */
    private function base64Encode($data)
    {
        return trim(base64_encode($data));
    }
 
    /**
     * Create HMAC hash using the SHA256 algorithm
     *
     * @param $data
     *
     * @return string
     */
    private function createDigest($data)
    {
        return trim(hash_hmac('sha256', $data, base64_decode($this->applicationSecret), true));
    }
}