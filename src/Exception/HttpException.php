<?php
declare(strict_types = 1);

namespace Enm\Bundle\JsonApi\Server\Exception;

use Enm\JsonApi\Exception\Exception;

/**
 * @author Philipp Marien <marien@eosnewmedia.de>
 */
class HttpException extends Exception
{
    /**
     * @var int
     */
    private $status;
    
    /**
     * @param int $status
     * @param \Exception $previous
     */
    public function __construct(int $status, \Exception $previous)
    {
        $this->status = $status;
        parent::__construct(
          $previous->getMessage(),
          $previous->getCode(),
          $previous->getPrevious()
        );
    }
    
    /**
     * @return int
     */
    public function getHttpStatus(): int
    {
        return $this->status;
    }
}
