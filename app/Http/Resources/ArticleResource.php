<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "_id" => $this->_id,
            "title" => $this->title,
            "category" => $this->category,
            "content" => $this->content,
            "tags" => $this->tags
        ];
    }
}
