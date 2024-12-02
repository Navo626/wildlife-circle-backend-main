<?php

use App\Http\Controllers\Api\Blog\AddBlogController;
use App\Http\Controllers\Api\Blog\AddCommentController;
use App\Http\Controllers\Api\Blog\AddLikeController;
use App\Http\Controllers\Api\Blog\DeleteBlogController;
use App\Http\Controllers\Api\Blog\DeleteCommentController;
use App\Http\Controllers\Api\Blog\EditBlogController;
use App\Http\Controllers\Api\Blog\EditCommentController;
use App\Http\Controllers\Api\Blog\OptimizeBlogController;
use App\Http\Controllers\Api\Blog\RetrieveBlogController;
use App\Http\Controllers\Api\Blog\RetrieveCommentController;
use App\Http\Controllers\Api\Blog\RetrieveLikeController;
use App\Http\Controllers\Api\Contact\ContactUsController;
use App\Http\Controllers\Api\Dashboard\DashboardController;
use App\Http\Controllers\Api\Gallery\AddGalleryController;
use App\Http\Controllers\Api\Gallery\DeleteGalleryController;
use App\Http\Controllers\Api\Gallery\EditGalleryController;
use App\Http\Controllers\Api\Gallery\RetrieveGalleryController;
use App\Http\Controllers\Api\Member\AddMemberController;
use App\Http\Controllers\Api\Member\DeleteMemberController;
use App\Http\Controllers\Api\Member\EditMemberController;
use App\Http\Controllers\Api\Member\RetrieveMemberController;
use App\Http\Controllers\Api\News\AddNewsController;
use App\Http\Controllers\Api\News\DeleteNewsController;
use App\Http\Controllers\Api\News\EditNewsController;
use App\Http\Controllers\Api\News\RetrieveNewsController;
use App\Http\Controllers\Api\Order\AddOrderController;
use App\Http\Controllers\Api\Order\RetrieveOrderController;
use App\Http\Controllers\Api\Payment\AuthPayment;
use App\Http\Controllers\Api\Payment\RetrieveInvoiceController;
use App\Http\Controllers\Api\Product\AddProductController;
use App\Http\Controllers\Api\Product\DeleteProductController;
use App\Http\Controllers\Api\Product\EditProductController;
use App\Http\Controllers\Api\Product\RetrieveProductController;
use App\Http\Controllers\Api\Project\AddProjectController;
use App\Http\Controllers\Api\Project\DeleteProjectController;
use App\Http\Controllers\Api\Project\EditProjectController;
use App\Http\Controllers\Api\Project\RetrieveProjectController;
use App\Http\Controllers\Api\Session\AddSessionController;
use App\Http\Controllers\Api\Session\DeleteSessionController;
use App\Http\Controllers\Api\Session\EditSessionController;
use App\Http\Controllers\Api\Session\RetrieveSessionController;
use App\Http\Controllers\Api\User\AuthUserController;
use App\Http\Controllers\Api\User\ChangePasswordController;
use App\Http\Controllers\Api\User\DeleteUserController;
use App\Http\Controllers\Api\User\ForgotPasswordController;
use App\Http\Controllers\Api\User\LoginUserController;
use App\Http\Controllers\Api\User\LogoutController;
use App\Http\Controllers\Api\User\RegisterAdminController;
use App\Http\Controllers\Api\User\RegisterUserController;
use App\Http\Controllers\Api\User\RetrieveUserController;
use App\Http\Controllers\Api\User\UpdateProfileController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::prefix('/auth')->group(function () {
    Route::post('/register', [RegisterUserController::class, 'registerUser']);
    Route::post('/login', [LoginUserController::class, 'loginUser']);
    Route::get('/check-token', [AuthUserController::class, 'checkToken'])->middleware('auth:sanctum');
    Route::post('/logout', [LogoutController::class, 'handle'])->middleware('auth:sanctum');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword']);
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);
    Route::post('/change-password', [ChangePasswordController::class, 'changePassword'])->middleware('auth:sanctum');
    Route::post('/update-profile', [UpdateProfileController::class, 'updateProfile'])->middleware('auth:sanctum');
});

// Admin routes
Route::prefix('/admin')->group(function () {
    Route::prefix('/dashboard')->group(function () {
        Route::get('/counts', [DashboardController::class, 'getCounts'])->middleware(['auth:sanctum', 'role:admin']);
        Route::get('/income', [DashboardController::class, 'getMonthlyIncome'])->middleware(['auth:sanctum', 'role:admin']);
    });

    Route::prefix('/user')->group(function () {
        Route::get('/', [RetrieveUserController::class, 'getUsers'])->middleware('auth:sanctum', 'role:admin');
        Route::post('/', [RegisterAdminController::class, 'registerAdmin'])->middleware('auth:sanctum', 'role:admin');
        Route::delete('/', [DeleteUserController::class, 'deleteUser'])->middleware('auth:sanctum', 'role:admin');
    });

    Route::prefix('/blog')->group(function () {
        Route::get('/', [RetrieveBlogController::class, 'getBlogs'])->middleware('auth:sanctum');
        Route::delete('/', [DeleteBlogController::class, 'deleteBlog'])->middleware('auth:sanctum');
    });

    Route::prefix('/news')->group(function () {
        Route::get('/', [RetrieveNewsController::class, 'getNews'])->middleware('auth:sanctum', 'role:admin');
        Route::post('/', [AddNewsController::class, 'addNews'])->middleware('auth:sanctum', 'role:admin');
        Route::put('/', [EditNewsController::class, 'editNews'])->middleware('auth:sanctum');
        Route::delete('/', [DeleteNewsController::class, 'deleteNews'])->middleware('auth:sanctum', 'role:admin');
    });

    Route::prefix('/project')->group(function () {
        Route::get('/', [RetrieveProjectController::class, 'getProjects'])->middleware('auth:sanctum', 'role:admin');
        Route::post('/', [AddProjectController::class, 'addProduct'])->middleware('auth:sanctum', 'role:admin');
        Route::put('/', [EditProjectController::class, 'editProject'])->middleware('auth:sanctum');
        Route::delete('/', [DeleteProjectController::class, 'deleteProject'])->middleware('auth:sanctum', 'role:admin');
    });

    Route::prefix('/session')->group(function () {
        Route::get('/', [RetrieveSessionController::class, 'getSessions'])->middleware('auth:sanctum', 'role:admin');
        Route::post('/', [AddSessionController::class, 'addSession'])->middleware('auth:sanctum', 'role:admin');
        Route::put('/', [EditSessionController::class, 'editSession'])->middleware('auth:sanctum', 'role:admin');
        Route::delete('/', [DeleteSessionController::class, 'deleteSession'])->middleware('auth:sanctum', 'role:admin');
    });

    Route::prefix('/gallery')->group(function () {
        Route::get('/', [RetrieveGalleryController::class, 'getGallery'])->middleware('auth:sanctum', 'role:admin');
        Route::post('/', [AddGalleryController::class, 'addImage'])->middleware('auth:sanctum', 'role:admin');
        Route::put('/', [EditGalleryController::class, 'editImage'])->middleware('auth:sanctum');
        Route::delete('/', [DeleteGalleryController::class, 'deleteImage'])->middleware('auth:sanctum', 'role:admin');
    });

    Route::prefix('/order')->group(function () {
        Route::get('/', [RetrieveOrderController::class, 'getOrders'])->middleware('auth:sanctum', 'role:admin');
        Route::get('/last', [RetrieveOrderController::class, 'getLastOrder'])->middleware('auth:sanctum', 'role:admin');
    });

    Route::prefix('/product')->group(function () {
        Route::get('/', [RetrieveProductController::class, 'getProducts'])->middleware('auth:sanctum', 'role:admin');
        Route::post('/', [AddProductController::class, 'addProduct'])->middleware('auth:sanctum', 'role:admin');
        Route::put('/', [EditProductController::class, 'editProduct'])->middleware('auth:sanctum');
        Route::delete('/', [DeleteProductController::class, 'deleteProduct'])->middleware('auth:sanctum', 'role:admin');
    });

    Route::prefix('/content')->group(function () {
        Route::prefix('/member')->group(function () {
            Route::get('/', [RetrieveMemberController::class, 'getMembers'])->middleware('auth:sanctum', 'role:admin');
            Route::post('/', [AddMemberController::class, 'addMember'])->middleware('auth:sanctum', 'role:admin');
            Route::put('/', [EditMemberController::class, 'editMember'])->middleware('auth:sanctum');
            Route::delete('/', [DeleteMemberController::class, 'deleteMember'])->middleware('auth:sanctum', 'role:admin');
        });
    });
});

// Public routes
Route::prefix('/blog')->group(function () {
    Route::get('/', [RetrieveBlogController::class, 'getBlogsPublic']);
    Route::post('/', [AddBlogController::class, 'addBlog'])->middleware('auth:sanctum');
    Route::put('/', [EditBlogController::class, 'editBlog'])->middleware('auth:sanctum');
    Route::delete('/', [DeleteBlogController::class, 'deleteBlog'])->middleware('auth:sanctum');
    Route::post('/optimize', [OptimizeBlogController::class, 'optimize'])->middleware('auth:sanctum');

    Route::prefix('/comment')->group(function () {
        Route::get('/', [RetrieveCommentController::class, 'getCommentsPublic']);
        Route::post('/', [AddCommentController::class, 'addComment'])->middleware('auth:sanctum');
        Route::put('/', [EditCommentController::class, 'editComment'])->middleware('auth:sanctum');
        Route::delete('/', [DeleteCommentController::class, 'deleteComment'])->middleware('auth:sanctum');
    });
    Route::prefix('/like')->group(function () {
        Route::get('/', [RetrieveLikeController::class, 'getLikeStatus'])->middleware('auth:sanctum');
        Route::post('/', [AddLikeController::class, 'handleLike'])->middleware('auth:sanctum');
    });
});
Route::get('/news', [RetrieveNewsController::class, 'getNewsPublic']);
Route::get('/projects', [RetrieveProjectController::class, 'getProjectsPublic']);
Route::get('/sessions', [RetrieveSessionController::class, 'getSessionsPublic']);
Route::get('/gallery', [RetrieveGalleryController::class, 'getGalleryPublic']);
Route::post('/order', [AddOrderController::class, 'addOrder']);
Route::get('/products', [RetrieveProductController::class, 'getProductsPublic']);
Route::post('/payment', [AuthPayment::class, 'handle']);
Route::get('/invoice', [RetrieveInvoiceController::class, 'generateInvoice']);
Route::get('/about', [RetrieveMemberController::class, 'getMembersPublic']);
Route::post('/contact', [ContactUsController::class, 'contactUs']);
