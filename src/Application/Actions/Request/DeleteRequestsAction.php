<?php
declare(strict_types=1);

namespace App\Application\Actions\Request;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;


class DeleteRequestsAction extends RequestAction
{
    /**
     * {@inheritdoc}
     */
    public function action(): Response
    {
        $form = $this->getFormData();

        $this->assertRequiredFields($form, ['ids' => gettype([])]);

        $str_list = '';
        foreach($form->ids as &$id)
        {
            if(!is_numeric($id)) throw new HttpBadRequestException($this->request, "Value '{$id}' is not numeric value.");
            $id = (int) $id;
            $str_list .= $id.', ';
        }
        
        $this->requestRepository->deleteList($form->ids);

        $this->logger->info("User id {$this->session->userId} deleted {$str_list}");

        return $this->respondWithData();
    }
}