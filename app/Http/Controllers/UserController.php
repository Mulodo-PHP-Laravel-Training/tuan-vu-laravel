<?php

namespace App\Http\Controllers;

use App\User as User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Input;

class UserController extends Controller
{

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        try
        {
            $userList = $this->user->all();
            foreach ($userList as $user)
            {
                $result['list'][] = [
                    'id'         => $user->id,
                    'first_name' => $user->first_name,
                    'last_name'  => $user->last_name,
                    'email'      => $user->email,
                ];
            }
            $this->formatApiSuccess($result);
        }
        catch (Exception $e)
        {
            $this->formatApiError($e->getMessage(), $e->getCode());
        }
        finally
        {
            return $this->responseApi();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|max:255',
                'last_name'  => 'required|max:255',
                'email'      => 'required|email|max:255|unique:users',
                'password'   => 'required|confirmed|min:6',
            ]);
            if ($validator->fails())
            {
                throw new Exception($validator->messages(), 400);
            }
            $this->user->first_name = $request->input('first_name');
            $this->user->last_name  = $request->input('last_name');
            $this->user->email      = $request->input('email');
            $this->user->password   = bcrypt($request->input['password']);

            if ($this->user->save())
            {
                $this->formatApiSuccess("Done");
            }
        }
        catch (Exception $e)
        {
            $this->formatApiError($e->getMessage(), $e->getCode());
        }
        finally
        {
            return $this->responseApi();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        try
        {
            $data = $this->user->find($id);
            
            if (!empty($data))
            {
                $this->formatApiSuccess($data->toArray());
            }
            else
            {
                throw new Exception("Member not found", 404);
            }
        }
        catch (Exception $e)
        {
            $this->formatApiError($e->getMessage(), $e->getCode());
        }
        finally
        {
            return $this->responseApi();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  int     $id
     *
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
