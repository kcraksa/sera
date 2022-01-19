<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Article;
use App\Http\Resources\ArticleResource;

class ArticleController extends Controller
{

    /**
     * 
     * Fetch article data (using MongoDB)
     * 
     * @OA\GET(
     *      path="/api/article",
     *      description="Fetch all data article.",
     *      tags={"Get All Article"},
     *      operationId="fetcharticle",
     *      security={{"apiAuth": {}}},
     *      @OA\Response(
     *          response=200,
     *          description="Fetch data success",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Schema(ref="#/components/schemas/apiResponse"),
     *              @OA\Examples(
     *                  example="response_while_fetch_data_article_success",
     *                  value={
     *                      "status": "success",
     *                      "message": "Data fetched successfully.",
     *                      "data": "Article data"
     *                  },
     *                  summary="Response while fetch data successfully"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Failed to fetch data",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Schema(ref="#/components/schemas/apiResponse"),
     *              @OA\Examples(
     *                  example="response_while_fetch_data_article_failed",
     *                  value={
     *                      "status": "error",
     *                      "message": "Failed to fetch data",
     *                      "data": ""
     *                  },
     *                  summary="Response while fetch data failed"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Request without Token",
     *          content={
     *              @OA\MediaType(
     *                  mediaType="text/html",
     *                  @OA\Schema(
     *                      example="Unauthorized."
     *                  )
     *              )
     *          }
     *      )
     * )
     * 
     * */

    public function index()
    {
        try {
            $data = Article::all();
            return $this->response("success", "Data fetched successfully.", ArticleResource::collection($data), 200); 
        } catch (\Exception $e) {
            return $this->response("error", "Failed to fetch data", "", 400);
        }
    }

    /**
     * 
     * Store article data (using MongoDB)
     * 
     * @OA\POST(
     *      path="/api/article/store",
     *      description="Store article data.",
     *      tags={"Store Article"},
     *      operationId="storearticle",
     *      security={{"apiAuth": {}}},
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="title",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="category",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="content",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="tags",
     *                      type="array",
     *                      @OA\Items()
     *                  ),
     *                  example={"title": "Title 1", "category": "Hukum", "content": "Content title 1", "tags": {"hukum", "pidana"}}
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Article created successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Schema(ref="#/components/schemas/apiResponse"),
     *              @OA\Examples(
     *                  example="response_while_store_data_article_success",
     *                  value={
     *                      "status": "success",
     *                      "message": "Article created successfully.",
     *                      "data": "Article data inserted"
     *                  },
     *                  summary="Response while store data successfully"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Failed to store data",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Schema(ref="#/components/schemas/apiResponse"),
     *              @OA\Examples(
     *                  example="response_while_store_data_article_failed",
     *                  value={
     *                      "status": "error",
     *                      "message": "Article created failed.",
     *                      "data": ""
     *                  },
     *                  summary="Response while store data failed"
     *              ),
     *              @OA\Examples(
     *                  example="response_while_store_data_article_failed_because_mandatory_field_empty",
     *                  value={
     *                      "status": "error",
     *                      "message": "Mandatory field cannot be empty",
     *                      "data": "Validation Information"
     *                  },
     *                  summary="Response while store data failed"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Request without Token",
     *          content={
     *              @OA\MediaType(
     *                  mediaType="text/html",
     *                  @OA\Schema(
     *                      example="Unauthorized."
     *                  )
     *              )
     *          }
     *      )
     * )
     * 
     * */
    public function store(Request $request)
    {
        try {
            $validation = $this->validate($request, [
                'title' => 'required',
                'category' => 'required',
                'content' => 'required',
                'tags' => 'required'
            ]);

            $article = Article::create([
                'title' => $request->title,
                'category' => $request->category,
                'content' => $request->content,
                'tags' => $request->tags
            ]);

            foreach ($request->tags as $tag) {
                $tag = $article->tags()->create(['tag' => $tag]);
            }

            $insert_id = $article->id;
            $data = Article::where('_id', '=', $insert_id)->get();

            return $this->response("success", "Article created successfully", ArticleResource::collection($data), 200);
        } catch (\Exception $e) {

            if ($e->getResponse()->original) {
                return $this->response("error", "Mandatory field cannot be empty", $e->getResponse()->original, 400);
            } else {
                return $this->response("error", "Article created failed.", "", 400);
            }
        }
            
    }

    /**
     * 
     * Fetch article data by ID (using MongoDB)
     * 
     * @OA\GET(
     *      path="/api/article/{id}/show",
     *      description="Fetch article data by ID.",
     *      tags={"Get Article By ID"},
     *      operationId="fetcharticlebyid",
     *      security={{"apiAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Article ID",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *          in="path",
     *          required=true
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Fetch data success",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Schema(ref="#/components/schemas/apiResponse"),
     *              @OA\Examples(
     *                  example="response_while_fetch_data_article_byid_success",
     *                  value={
     *                      "status": "success",
     *                      "message": "Data fetched successfully.",
     *                      "data": "Article data"
     *                  },
     *                  summary="Response while fetch data successfully"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Failed to fetch data",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Schema(ref="#/components/schemas/apiResponse"),
     *              @OA\Examples(
     *                  example="response_while_fetch_data_article_byid_failed",
     *                  value={
     *                      "status": "error",
     *                      "message": "Failed to fetch data",
     *                      "data": ""
     *                  },
     *                  summary="Response while fetch data failed"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Request without Token",
     *          content={
     *              @OA\MediaType(
     *                  mediaType="text/html",
     *                  @OA\Schema(
     *                      example="Unauthorized."
     *                  )
     *              )
     *          }
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Failed to fetch data because ID not given",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Schema(ref="#/components/schemas/apiResponse"),
     *              @OA\Examples(
     *                  example="response_while_fetch_data_article_byid_failed_because_no_id",
     *                  value={
     *                      "status": "error",
     *                      "message": "Page Not Found",
     *                      "data": ""
     *                  },
     *                  summary="Response while fetch data failed because ID not given"
     *              )
     *          )
     *      ),
     * )
     * 
     * */
    public function show($id)
    {
        try {

            if (empty($id)) {
                return $this->response("error", "Failed to fetch data", "", 400);
            }

            $data = Article::where('_id', '=', $id)->get();
            return $this->response("success", "Data fetched successfully.", ArticleResource::collection($data), 200); 
        } catch (\Exception $e) {
            return $this->response("error", "Failed to fetch data", "", 400);
        }
    }

    /**
     * 
     * Update article data (using MongoDB)
     * 
     * @OA\POST(
     *      path="/api/article/{ID}/update",
     *      description="Update article data.",
     *      tags={"Update Article"},
     *      operationId="updatearticle",
     *      security={{"apiAuth": {}}},
     *      @OA\Parameter(
     *          name="ID",
     *          description="Article ID",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *          in="path",
     *          required=true
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="title",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="category",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="content",
     *                      type="string"
     *                  ),
     *                  @OA\Property(
     *                      property="tags",
     *                      type="array",
     *                      @OA\Items()
     *                  ),
     *                  example={"title": "Title 1", "category": "Hukum", "content": "Content title 1", "tags": {"hukum", "pidana"}}
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Article updated successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Schema(ref="#/components/schemas/apiResponse"),
     *              @OA\Examples(
     *                  example="response_while_update_data_article_success",
     *                  value={
     *                      "status": "success",
     *                      "message": "Article updated successfully.",
     *                      "data": "Article data inserted"
     *                  },
     *                  summary="Response while update data successfully"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Failed to update data",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Schema(ref="#/components/schemas/apiResponse"),
     *              @OA\Examples(
     *                  example="response_while_update_data_article_failed",
     *                  value={
     *                      "status": "error",
     *                      "message": "Article updated failed.",
     *                      "data": ""
     *                  },
     *                  summary="Response while update data failed"
     *              ),
     *              @OA\Examples(
     *                  example="response_while_update_data_article_failed_because_mandatory_field_empty",
     *                  value={
     *                      "status": "error",
     *                      "message": "Mandatory field cannot be empty",
     *                      "data": "Validation Information"
     *                  },
     *                  summary="Response while update data failed"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Request without Token",
     *          content={
     *              @OA\MediaType(
     *                  mediaType="text/html",
     *                  @OA\Schema(
     *                      example="Unauthorized."
     *                  )
     *              )
     *          }
     *      )
     * )
     * 
     * */
    public function update(Request $request, $id)
    {
        try {
            $validation = $this->validate($request, [
                'title' => 'required',
                'category' => 'required',
                'content' => 'required',
                'tags' => 'required'
            ]);

            $is_exists = Article::where("_id", "=", $id)->count();
            if ($is_exists < 1) {
                return $this->response("error", "Article updated failed.", "", 400);
            }

            $article = Article::where("_id", "=", $id)->update([
                'title' => $request->title,
                'category' => $request->category,
                'content' => $request->content,
                'tags' => $request->tags
            ]);

            Article::where("_id", "=", $id)->unset('tags');

            foreach ($request->tags as $tag) {
                $tag = Article::where("_id", "=", $id)->first()->tags()->create(['tag' => $tag]);
            }

            $data = Article::where('_id', '=', $id)->get();

            return $this->response("success", "Article updated successfully", ArticleResource::collection($data), 200);
        } catch (\Exception $e) {

            if ($e->getResponse()->original) {
                return $this->response("error", "Mandatory field cannot be empty", $e->getResponse()->original, 400);
            } else {
                return $this->response("error", "Article updated failed.", "", 400);
            }
        }
    }

    /**
     * 
     * Delete article data (using MongoDB)
     * 
     * @OA\POST(
     *      path="/api/article/{ID}/delete",
     *      description="Delete article data.",
     *      tags={"Delete Article"},
     *      operationId="deletearticle",
     *      security={{"apiAuth": {}}},
     *      @OA\Parameter(
     *          name="ID",
     *          description="Article ID",
     *          @OA\Schema(
     *              type="string"
     *          ),
     *          in="path",
     *          required=true
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Article deleted successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Schema(ref="#/components/schemas/apiResponse"),
     *              @OA\Examples(
     *                  example="response_while_delete_data_article_success",
     *                  value={
     *                      "status": "success",
     *                      "message": "Article deleted successfully.",
     *                      "data": ""
     *                  },
     *                  summary="Response while delete data successfully"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Failed to delete data",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Schema(ref="#/components/schemas/apiResponse"),
     *              @OA\Examples(
     *                  example="response_while_delete_data_article_failed",
     *                  value={
     *                      "status": "error",
     *                      "message": "Delete article failed.",
     *                      "data": ""
     *                  },
     *                  summary="Response while delete data failed"
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Request without Token",
     *          content={
     *              @OA\MediaType(
     *                  mediaType="text/html",
     *                  @OA\Schema(
     *                      example="Unauthorized."
     *                  )
     *              )
     *          }
     *      )
     * )
     * 
     * */
    public function delete($id)
    {
        try {
            $is_exists = Article::where("_id", "=", $id)->count();
            if ($is_exists < 1) {
                return $this->response("error", "Delete Article failed.", "", 400);
            }

            $article = Article::where('_id', '=', $id)->first();
            $article->delete();
            return $this->response("success", "Article deleted successfully", "", 200);
        } catch (\Exception $e) {
            return $this->response("error", "Delete Article failed.", "", 400);
        }
    }

    protected function response($status, $message, $data, $responseCode) 
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $responseCode);
    }
}
