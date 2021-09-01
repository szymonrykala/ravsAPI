<?php
declare(strict_types=1);

namespace App\Application\Actions\Request;

use Psr\Http\Message\ResponseInterface as Response;

class ListRequestsAction extends RequestAction
{
    /**
     * {@inheritdoc}
     */
    public function action(): Response
    {
        $searchParams = [];

        if(isset($this->args['subject']))
        {
            $subject = $this->resolveArg('subject');
            $searchParams['endpoint'] = '/'.$subject;

            if(isset($this->args['subjectId']))
            {
                $searchParams['endpoint'] .= '/'.$this->resolveArg('subjectId');
            }
        }


        $fields = [
            'user_id' => 'userId',
            'method' => 'method'
        ];
        foreach($fields as $param => $field)
        {
            if(isset($_GET[$field])){
                $searchParams[$param] = $_GET[$field];
            }
        }

        $repoState = $this->requestRepository
                    ->where($searchParams)
                    ->withDates($this->collectDatesSearchParam());

        $data = [];

        $pager = $this->collectPageQueryParams();

        if($pager->isset){
            $data = $repoState->page($pager->page, $pager->limit);
        }else{
            $data = $repoState->all();
        }

        $this->logger->info("User id {$this->session->userId} listed requests");

        return $this->respondWithData($data);
    }
}