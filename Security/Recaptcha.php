<?php
namespace AZ\Helpers\Security;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


/**
 * Recaptcha
 * 
 * <code>
 * <div class="g-recaptcha" data-sitekey="<?= $recaptcha_sitekey ?>"></div>
 * </code>
 * 
 * <code>
 * $recaptcha = new Recaptcha();
 * $is_human = $recaptcha->validate($request);
 * ...
 * $site_key = $recaptcha->getSiteKey();
 * </code>
 * 
 */
class Recaptcha
{


    /**
     * Check if it is human.
     * 
     * @param \Illuminate\Http\Request $request
     * @return bool|null
     */
    public function validate(Request $request): ?bool {

        if (!$request->has('g-recaptcha-response')) {

            return null;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $this->getSecretKey(),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ]);

        $data = $response->json();

    
        if (isset($data['success']) && $data['success']) {
            return true;
        }

        return false;

    }

    
    /**
     * Return from RECAPTCHA_SITE_KEY (.env)
     * 
     * @throws Exception
     * @return string
     */
    public function getSiteKey(): string {

        $result = env('RECAPTCHA_SITE_KEY');

        if (empty($result)) {
            throw new Exception("RECAPTCHA_SITE_KEY is not defined in .env", 1);
        }

        return $result;
    }


    /**
     * Return from RECAPTCHA_SECRET_KEY (.env)
     * 
     * @throws \Exception
     * @return string
     */
    protected function getSecretKey(): string {

        $result = env('RECAPTCHA_SECRET_KEY');

        if (empty($result)) {
            throw new Exception("RECAPTCHA_SECRET_KEY is not defined in .env", 1);
        }

        return $result;
    }
    


    
}