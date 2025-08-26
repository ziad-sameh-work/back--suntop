<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Modules\Users\Models\UserCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class AdminUserController extends Controller
{
    /**
     * Display users list page
     */
    public function index(Request $request)
    {
        // Get filters from request
        $search = $request->get('search', '');
        $role = $request->get('role', '');
        $status = $request->get('status', '');
        $category = $request->get('category', '');
        $perPage = $request->get('per_page', 15);

        // Build query
        $query = User::with(['userCategory'])
            ->where('role', '!=', 'admin'); // Don't show other admins

        // Apply filters
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('email', 'LIKE', '%' . $search . '%')
                  ->orWhere('username', 'LIKE', '%' . $search . '%')
                  ->orWhere('phone', 'LIKE', '%' . $search . '%');
            });
        }

        if ($role) {
            $query->where('role', $role);
        }

        if ($status !== '') {
            $query->where('is_active', $status == 'active');
        }

        if ($category) {
            $query->where('user_category_id', $category);
        }

        // Get paginated results
        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Get statistics
        $stats = $this->getUsersStats();

        // Get filter options
        $categories = UserCategory::where('is_active', true)->get();

        return view('admin.users.index', compact(
            'users',
            'stats',
            'categories',
            'search',
            'role',
            'status',
            'category',
            'perPage'
        ));
    }

    /**
     * Show user details
     */
    public function show($id)
    {
        $user = User::with(['userCategory'])->findOrFail($id);
        
        // Get user orders and statistics
        $userStats = $this->getUserDetailsStats($user);

        return view('admin.users.show', compact('user', 'userStats'));
    }

    /**
     * Show create user form
     */
    public function create()
    {
        $categories = UserCategory::where('is_active', true)->get();
        return view('admin.users.create', compact('categories'));
    }

    /**
     * Store new user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:customer,merchant',
            'user_category_id' => 'nullable|exists:user_categories,id',
            'is_active' => 'boolean',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $validated['password'] = Hash::make($validated['password']);
            $validated['is_active'] = $request->has('is_active');
            
            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                $image = $request->file('profile_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/users'), $imageName);
                $validated['profile_image'] = 'uploads/users/' . $imageName;
            }

            $user = User::create($validated);

            DB::commit();

            return redirect()->route('admin.users.show', $user->id)
                ->with('success', 'تم إنشاء المستخدم بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء المستخدم: ' . $e->getMessage());
        }
    }

    /**
     * Show edit user form
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $categories = UserCategory::where('is_active', true)->get();
        
        return view('admin.users.edit', compact('user', 'categories'));
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:customer,merchant',
            'user_category_id' => 'nullable|exists:user_categories,id',
            'is_active' => 'boolean',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $validated['is_active'] = $request->has('is_active');
            
            // Only update password if provided
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                // Delete old image
                if ($user->profile_image && file_exists(public_path($user->profile_image))) {
                    unlink(public_path($user->profile_image));
                }

                $image = $request->file('profile_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/users'), $imageName);
                $validated['profile_image'] = 'uploads/users/' . $imageName;
            }

            $user->update($validated);

            DB::commit();

            return redirect()->route('admin.users.show', $user->id)
                ->with('success', 'تم تحديث بيانات المستخدم بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث المستخدم: ' . $e->getMessage());
        }
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Don't allow deleting admin users
            if ($user->role === 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن حذف المديرين'
                ], 403);
            }

            // Delete profile image
            if ($user->profile_image && file_exists(public_path($user->profile_image))) {
                unlink(public_path($user->profile_image));
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف المستخدم بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف المستخدم: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle user status
     */
    public function toggleStatus($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Don't allow deactivating admin users
            if ($user->role === 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن تعطيل المديرين'
                ], 403);
            }

            $user->is_active = !$user->is_active;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => $user->is_active ? 'تم تفعيل المستخدم' : 'تم تعطيل المستخدم',
                'is_active' => $user->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الحالة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset user password
     */
    public function resetPassword($id)
    {
        try {
            $user = User::findOrFail($id);
            $newPassword = 'password123'; // Default password
            
            $user->password = Hash::make($newPassword);
            $user->password_changed_at = now();
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'تم إعادة تعيين كلمة المرور بنجاح',
                'new_password' => $newPassword
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إعادة تعيين كلمة المرور: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        try {
            $users = User::whereIn('id', $validated['user_ids'])
                ->where('role', '!=', 'admin') // Protect admin users
                ->get();

            $count = 0;

            foreach ($users as $user) {
                switch ($validated['action']) {
                    case 'activate':
                        $user->update(['is_active' => true]);
                        $count++;
                        break;
                    case 'deactivate':
                        $user->update(['is_active' => false]);
                        $count++;
                        break;
                    case 'delete':
                        if ($user->profile_image && file_exists(public_path($user->profile_image))) {
                            unlink(public_path($user->profile_image));
                        }
                        $user->delete();
                        $count++;
                        break;
                }
            }

            $actionText = [
                'activate' => 'تفعيل',
                'deactivate' => 'تعطيل',
                'delete' => 'حذف'
            ];

            return response()->json([
                'success' => true,
                'message' => "تم {$actionText[$validated['action']]} {$count} مستخدم بنجاح"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تنفيذ العملية: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get users statistics
     */
    private function getUsersStats()
    {
        $totalUsers = User::where('role', '!=', 'admin')->count();
        $activeUsers = User::where('role', '!=', 'admin')->where('is_active', true)->count();
        $inactiveUsers = User::where('role', '!=', 'admin')->where('is_active', false)->count();
        $customers = User::where('role', 'customer')->count();
        $merchants = User::where('role', 'merchant')->count();
        $recentUsers = User::where('role', '!=', 'admin')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();

        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'inactive_users' => $inactiveUsers,
            'customers' => $customers,
            'merchants' => $merchants,
            'recent_users' => $recentUsers,
            'active_percentage' => $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 1) : 0
        ];
    }

    /**
     * Get user details statistics
     */
    private function getUserDetailsStats($user)
    {
        // This will be used when we implement orders
        return [
            'total_orders' => 0, // Order::where('user_id', $user->id)->count(),
            'total_spent' => $user->total_purchase_amount ?? 0,
            'avg_order_value' => 0,
            'last_order_date' => null,
            'favorite_category' => 'غير محدد',
            'loyalty_points' => 0
        ];
    }
}
