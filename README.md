#Authenticate to the Sinch Javascript SDK from your PHP Backend

_Shoutout to Rob Holmes from [i6systems.com](http://i6systems.com) for putting together this sample code for us!_

Define the following class to generate a user ticket to authenticate to the Sinch Javascript SDK:

    class SinchTicketGenerator
    {

        private $applicationKey; 
        private $applicationSecret;
 
        public function __construct($applicationKey, $applicationSecret)
        {
            $this->applicationKey = $applicationKey;
            $this->applicationSecret = $applicationSecret;
        }
 
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
     
        private function base64Encode($data)
        {
            return trim(base64_encode($data));
        }
     
        private function createDigest($data)
        {
            return trim(hash_hmac('sha256', $data, base64_decode($this->applicationSecret), true));
        }
    }
    
You can use the above class like so:

    $generator = new SinchTicketGenerator('YOUR_APP_KEY', 'YOUR_APP_SECRET');
    $signedUserTicket = $generator->generateTicket('YOUR_USERNAME', new DateTime(), 3600);
    
"YOUR_USERNAME" is any way you uniquely identify your users, and "3600" is the number of seconds the ticket will expire in.

If you don't yet have an app key and secret from Sinch, you can get them for free here: [www.sinch.com/signup](https://www.sinch.com/signup)

Once you've successfully generated the ticket, you can use it to securely start the Sinch Client. Follow any of our client-side javascript tutorials to use the Sinch Client to make phone calls and send instant messages:

- Phone calling [www.sinch.com/tutorials/using-sinch-js-sdk-make-voice-calls](https://www.sinch.com/tutorials/using-sinch-js-sdk-make-voice-calls/)
- App to app calling [www.sinch.com/tutorials/turn-browser-phone-js-sdk](https://www.sinch.com/tutorials/turn-browser-phone-js-sdk/)
- Instant messaging [www.sinch.com/tutorials/build-instant-messaging-app-sinch-javascript](https://www.sinch.com/tutorials/build-instant-messaging-app-sinch-javascript/)
