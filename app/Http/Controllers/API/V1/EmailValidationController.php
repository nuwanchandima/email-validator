<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Traits\Api\ApiHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class EmailValidationController extends Controller
{
    use ApiHelper;
    /**
     * validateEmail
     *
     * @param  mixed $request
     * @return void
     */
    public function validateEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email:rfc,dns'],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Invalid email format.', Response::HTTP_BAD_REQUEST);
        }

        $email = $request->input('email');
        if (!$this->verifyEmail($email)) {
            return $this->errorResponse('The email address is not deliverable.', Response::HTTP_BAD_REQUEST);
        }
        return $this->successResponse('The email address is valid.', Response::HTTP_OK);
    }

    /**
     * verifyEmail
     *
     * @param  mixed $email
     * @return bool
     */
    private function verifyEmail($email): bool
    {
        // Extract domain and check MX records
        list($user, $domain) = explode('@', $email);

        if (!checkdnsrr($domain, 'MX')) {
            return false;
        }

        // Perform SMTP validation
        return $this->smtpCheck($domain, $email);
    }
    /**
     * smtpCheck
     *
     * @param  mixed $domain
     * @param  mixed $email
     * @return bool
     */
    private function smtpCheck($domain, $email): bool
    {
        $from = config('mail.from.address');

        // Open socket connection to the mail server
        $connection = fsockopen($domain, 25, $errno, $errstr, 10);
        if (!$connection) {
            return false;
        }

        $data = [
            "HELO $domain\r\n",
            "MAIL FROM: <$from>\r\n",
            "RCPT TO: <$email>\r\n",
            "QUIT\r\n"
        ];

        foreach ($data as $command) {
            fwrite($connection, $command);
            $response = fgets($connection, 1024);

            if (strpos($response, '550') !== false) {
                fclose($connection);
                return false;
            }
        }

        fclose($connection);
        return true;
    }
}
