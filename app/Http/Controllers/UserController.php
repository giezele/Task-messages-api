<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use App\Transformers\UserTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Fractal\Fractal;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var UserTransformer
     */
    private $userTransformer;

    function __construct(Manager $fractal, UserTransformer $userTransformer)
    {
        $this->fractal = $fractal;
        $this->userTransformer = $userTransformer;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $paginator = User::paginate(10);

        // $users = User::all(); // Get users from DB
        // $users = new Collection($users, $this->userTransformer); // Create a resource collection transformer
        $users = new Collection($paginator->items(), $this->userTransformer);
        $users->setPaginator(new IlluminatePaginatorAdapter($paginator));
        
        $this->fractal->parseIncludes($request->get('include', '')); // parse includes
        $users = $this->fractal->createData($users); // Transform data

        return $users->toArray(); // Get transformed array of data

        // $users = User::all()->transformWith(new UserTransformer())->toArray();
        // return $users;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $user = User::find($id);
        $user = new Item($user, $this->userTransformer);
        $this->fractal->parseIncludes($request->get('include', '')); // parse includes
        $user = $this->fractal->createData($user); 

        return $user->toArray(); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function usersAll(User $user)
    {
        $users = $user->all();

        return fractal()
    		->collection($users)
    		->transformWith(new UserTransformer)
    		->toArray();
    }

    public function userOwnTasks(User $user){
        $user = $user->find(Auth::user()->id);
     
        return fractal()
            ->item($user)
            ->transformWith(new UserTransformer)
            ->includeTasks()
            ->toArray();
    }
}
