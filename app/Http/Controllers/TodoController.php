<?php
/*
######################### Advance ORM Example ###################################################
$usersWithTodos = User::whereHas('todos', function ($query) {
    $query->where('user_id', 11); // تحديد المستخدم الذي يملك todos معينة (مثال: user_id = 11)
})
->with('todos') // جلب المهام الخاصة بكل مستخدم
->get(); // استرجاع البيانات


1. الحصول على المستخدمين الذين لديهم مهام باستخدام JOIN
use Illuminate\Support\Facades\DB;

$usersWithTodos = DB::table('users')
    ->join('todos', 'users.id', '=', 'todos.user_id') // ربط جدول users مع todos
    ->select('users.id', 'users.name', 'users.email', 'todos.*') // تحديد الأعمدة المطلوبة
    ->get();

dd($usersWithTodos); // طباعة النتيجة


2. الحصول على المستخدمين الذين لديهم مهام باستخدام WHERE EXISTS
$usersWithTodos = DB::table('users')
    ->whereExists(function ($query) {
        $query->select(DB::raw(1))
              ->from('todos')
              ->whereRaw('todos.user_id = users.id');  
    })
    ->get();

dd($usersWithTodos);



 هذا الكود سيعرض جميع المستخدمين الذين ليس لديهم أي مهام في جدول todos.
use Illuminate\Support\Facades\DB;

$usersWithoutTodos = DB::table('users')
    ->leftJoin('todos', 'users.id', '=', 'todos.user_id')
    ->whereNull('todos.id') // التأكد من أن المستخدم لا يملك مهام
    ->select('users.*') // تحديد الأعمدة المطلوبة من جدول المستخدمين
    ->get();

dd($usersWithoutTodos); // طباعة النتيجة للفحص


✅ 1. الحصول على المستخدمين الذين لديهم مهام باستخدام JOIN
php
Copy
Edit
use Illuminate\Support\Facades\DB;

$usersWithTodos = DB::table('users')
    ->join('todos', 'users.id', '=', 'todos.user_id') // ربط جدول users مع todos
    ->select('users.id', 'users.name', 'users.email', 'todos.*') // تحديد الأعمدة المطلوبة
    ->get();

dd($usersWithTodos); // طباعة النتيجة
✅ 2. الحصول على المستخدمين الذين لديهم مهام باستخدام WHERE EXISTS
php
Copy
Edit
$usersWithTodos = DB::table('users')
    ->whereExists(function ($query) {
        $query->select(DB::raw(1))
              ->from('todos')
              ->whereRaw('todos.user_id = users.id');  
    })
    ->get();

dd($usersWithTodos);
🔍 شرح الكود:
JOIN:

يستخدم join('todos', 'users.id', '=', 'todos.user_id') لجلب المستخدمين المرتبطين بمهام.
select('users.id', 'users.name', 'users.email', 'todos.*') يحدد الأعمدة المطلوبة.
->get(); يجلب البيانات.
WHERE EXISTS:

يستخدم whereExists() للتأكد من أن هناك سجلًا في todos يطابق users.id.
select(DB::raw(1)) يستخدم للتحقق من وجود البيانات دون الحاجة إلى جلب الأعمدة.
whereRaw('todos.user_id = users.id') يربط المستخدمين بالمهام.
->get(); يجلب البيانات.
✅ هذا الكود سيعيد المستخدمين الذين لديهم مهام باستخدام طريقتين مختلفتين! 🚀


1️⃣ INNER JOIN (JOIN) - المستخدمون الذين لديهم مهام
$usersWithTodos = DB::table('users')
    ->join('todos', 'users.id', '=', 'todos.user_id') // INNER JOIN
    ->select('users.id', 'users.name', 'users.email', 'todos.*')
    ->get();

dd($usersWithTodos);
✅ يجلب فقط المستخدمين الذين لديهم مهام في جدول todos.

2️⃣ LEFT JOIN - جميع المستخدمين مع أو بدون مهام
$usersWithOrWithoutTodos = DB::table('users')
    ->leftJoin('todos', 'users.id', '=', 'todos.user_id') // LEFT JOIN
    ->select('users.id', 'users.name', 'users.email', 'todos.*')
    ->get();

dd($usersWithOrWithoutTodos);
✅ يجلب جميع المستخدمين، حتى الذين لا يملكون مهام (القيم الفارغة NULL تعني عدم وجود مهام).

3️⃣ RIGHT JOIN - جميع المهام مع أو بدون مستخدمين
$todosWithUsers = DB::table('users')
    ->rightJoin('todos', 'users.id', '=', 'todos.user_id') // RIGHT JOIN
    ->select('users.id', 'users.name', 'users.email', 'todos.*')
    ->get();

dd($todosWithUsers);
✅ يجلب جميع المهام، حتى لو لم يكن لها مستخدم مسجل.

4️⃣ CROSS JOIN - جميع التباديل الممكنة بين المستخدمين والمهام
$crossJoinUsersTodos = DB::table('users')
    ->crossJoin('todos') // CROSS JOIN
    ->select('users.id', 'users.name', 'todos.title')
    ->get();

dd($crossJoinUsersTodos);
✅ ينتج جميع التوليفات الممكنة بين المستخدمين والمهام، حتى لو لم تكن هناك علاقة بينهم.

5️⃣ FULL OUTER JOIN - (غير مدعوم مباشرة في Laravel Query Builder، لكن يمكن محاكاته بـ UNION)
$fullOuterJoin = DB::table('users')
    ->leftJoin('todos', 'users.id', '=', 'todos.user_id')
    ->select('users.id', 'users.name', 'todos.title')
    ->union(
        DB::table('users')
            ->rightJoin('todos', 'users.id', '=', 'todos.user_id')
            ->select('users.id', 'users.name', 'todos.title')
    )
    ->get();

dd($fullOuterJoin);
✅ يجلب جميع المستخدمين والمهام، حتى لو لم يكن بينهم ارتباط.

6️⃣ WHERE EXISTS - المستخدمون الذين لديهم مهام (بديل عن JOIN)
$usersWithTodos = DB::table('users')
    ->whereExists(function ($query) {
        $query->select(DB::raw(1))
              ->from('todos')
              ->whereRaw('todos.user_id = users.id');  
    })
    ->get();

dd($usersWithTodos);
✅ طريقة بديلة للحصول على المستخدمين الذين لديهم مهام بدون استخدام JOIN.

7️⃣ WHERE NOT EXISTS - المستخدمون الذين لا يملكون مهام
$usersWithoutTodos = DB::table('users')
    ->whereNotExists(function ($query) {
        $query->select(DB::raw(1))
              ->from('todos')
              ->whereRaw('todos.user_id = users.id');  
    })
    ->get();

dd($usersWithoutTodos);
✅ يجلب المستخدمين الذين لا يملكون أي مهام مسجلة.



$todosWithUsers = DB::table('todos')
    ->join('users', 'todos.user_id', '=', 'users.id') // Join with users table
    ->select('todos.*', 'users.name as user_name') // Select todo fields and user name
    ->orderBy('todos.created_at', 'desc') // Order by latest created tasks
    ->limit(10) // Limit the number of results
    ->get(); //->sql();

    transform to sql SELECT todos.*, users.name AS user_name
FROM todos
JOIN users ON todos.user_id = users.id
ORDER BY todos.created_at DESC
LIMIT 10;


$todosPaginated = DB::table('todos')
    ->join('users', 'todos.user_id', '=', 'users.id') // ربط جدول todos بجدول users باستخدام user_id
    ->select('todos.*', 'users.name as user_name') // اختيار كل الأعمدة من جدول todos وأيضا اسم المستخدم من جدول users
    ->simplePaginate(2); // تقسيم النتائج إلى صفحات مع عرض 2 سجل في كل صفحة
dd($todosPaginated);

get completed todos and users using subquery
$completedTodos = DB::table('todos')
    ->where('completed', 1) // تحديد أن المهمة مكتملة (completed = 1)
    ->whereIn('user_id', function ($query) {
        $query->select('id')
              ->from('users')
              ->where('name', 'LIKE', '%Emilio%'); // البحث عن المستخدمين الذين تحتوي أسماؤهم على "Emilio"
    })
    ->get(); // إرجاع النتائج
dd($completedTodos); // عرض النتائج

*/
namespace App\Http\Controllers;



use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    // عرض قائمة المهام
    public function index(Request $request)
{
    // استقبال قيمة البحث من الطلب
    $todos = Todo::orderBy('created_at', 'desc')->paginate(5);
    return view('todos.index', compact('todos'));
}

    // إضافة مهمة جديدة
    public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
    ]);

    $todo = Todo::create([
        'title' => $request->title,
        'description' => $request->description,
        'completed' => 0,
        'user_id' => auth()->id(),
    ]);

    return redirect()->route('todos.index')->with('success', 'Todo added successfully!');

}


    // تحديث بيانات المهمة
    public function update(Request $request, Todo $todo, $id )
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $todo = Todo::findOrFail($id);
        $todo->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        // return response()->json($todo);

        return response()->json([
            'id' => $todo->id,
            'title' => $todo->title,
            'description' => $todo->description,
        ]);
    }

    // حذف المهمة
    public function destroy(Todo $todo, $id )
    {
        $todo = Todo::findOrFail($id);
        $todo->delete();
        return response()->json(['message' => 'Todo deleted successfully']);
    }

    // تحديد المهمة كمكتملة أو غير مكتملة
    public function toggleComplete(Todo $todo,$id)
    {
        // $todo->completed = !$todo->completed; // عكس قيمة `completed`
        // $todo->save();
        
        // return response()->json([
        // 'success' => true,
        // 'completed' => $todo->completed ]);
        $todo = Todo::findOrFail($id);
        $todo->completed = !$todo->completed; // تبديل الحالة
        $todo->save();
        return response()->json([
            'id' => $todo->id,
            'completed' => $todo->completed,
        ]);
    }
    public function show($id)
    {
        $todo = Todo::findOrFail($id);
        return response()->json($todo); // إرجاع بيانات التودو بصيغة JSON
    }

    public function trashed()
    {
        // Fetch soft-deleted todos
        $todos = Todo::onlyTrashed()->get(); // استرجاع المهام المحذوفة فقط
       
        return view('todos.trashed', compact('todos'));
    }
    

    public function restore($todo) {

        // $todo = Todo::withTrashed()->find($id);
        // if ($todo) {
        //     $todo->restore();
        //     return response()->json(['message' => 'Todo restored successfully.']);
        // }
        // return response()->json(['message' => 'Todo not found.'], 404);
        $todo = Todo::withTrashed()->findOrFail($todo);
        $todo->restore();
        return response()->json(['success' => true, 'message' => 'Todo restored successfully!']);
    }    
    
    public function forceDelete($todo) {
        $todo = Todo::withTrashed()->findOrFail($todo);
        $todo->forceDelete();
        return response()->json(['success' => true, 'message' => 'Todo deleted successfully!']);
    }
    


}
