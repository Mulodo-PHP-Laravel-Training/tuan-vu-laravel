<?php

namespace App\Http\Controllers;

use App\User as User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends RestfulController
{

    private $model;

    public function __construct()
    {
        $this->middleware('restfulAuth', ['except' => ['index', 'show']]);
        $this->model = new User;
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
            $userList = $this->model->all(['id', 'first_name', 'last_name']);
            foreach ($userList as $user)
            {
                $result['list'][] = [
                    'id'         => $user->id,
                    'first_name' => $user->first_name,
                    'last_name'  => $user->last_name,
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
                'password'   => 'required|min:6',
            ]);
            if ($validator->fails())
            {
                throw new Exception($validator->messages(), 400);
            }
            $this->model->first_name = $request->input('first_name');
            $this->model->last_name  = $request->input('last_name');
            $this->model->email      = $request->input('email');
            $this->model->password   = bcrypt($request->input['password']);

            if ($this->model->save())
            {
                $this->formatApiSuccess();
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
            $user = $this->model->find($id);
            if (empty($user))
            {
                throw new Exception("Member not found", 404);
            }

            $this->formatApiSuccess($user->toArray());
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
        try
        {
            $user = $this->model->find($id);
            if (empty($user))
            {
                throw new Exception("Member not found", 404);
            }
            
            $validator = Validator::make($request->all(), [
                'first_name' => 'max:255',
                'last_name'  => 'max:255',
                'email'      => 'email|max:255|unique:users',
                'password'   => 'min:6',
            ]);
            if ($validator->fails())
            {
                throw new Exception($validator->messages(), 400);
            }
            
            $firstName = $request->input('first_name');
            $lastName  = $request->input('last_name');
            $email     = $request->input('email');
            $password  = $request->input('password');

            if (!empty($firstName))
            {
                $user->first_name = $firstName;
            }
            elseif (!empty($lastName))
            {
                $user->last_name = $lastName;
            }
            elseif (!empty($email))
            {
                $user->email = $email;
            }
            elseif (!empty($password))
            {
                $user->password = bcrypt($password);
            }

            if ($user->update())
            {
                $this->formatApiSuccess();
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
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        try
        {
            $user = $this->model->find($id);
            if (empty($user))
            {
                throw new Exception("Member not found", 404);
            }

            if ($user->delete())
            {
                $this->formatApiSuccess();
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
}
