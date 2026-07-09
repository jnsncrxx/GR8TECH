<?php $__env->startSection('title', 'Login'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-[linear-gradient(135deg,_rgba(45,147,68,0.14)_0%,_rgba(255,255,255,0.98)_50%,_rgba(237,28,36,0.12)_100%)] dark:bg-slate-950">
    <div class="grid min-h-screen lg:grid-cols-2">
        <section class="relative overflow-hidden bg-[#2D9344] px-6 py-8 sm:px-10 sm:py-10 lg:px-12 lg:py-12 flex flex-col justify-between text-[#FFFFFF] dark:bg-slate-950 dark:text-white">
            <div class="absolute inset-0 opacity-15">
                <div class="absolute left-10 top-10 h-40 w-40 rounded-full border border-white/40"></div>
                <div class="absolute left-24 top-24 h-72 w-72 rounded-full border border-white/20"></div>
                <div class="absolute bottom-8 right-8 h-48 w-48 rounded-full border border-black/30"></div>
            </div>

            <div class="relative z-10 flex items-start justify-between gap-6">
                <div>
                    <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black leading-none tracking-tight text-white">GR8 Tech</h1>
                    <p class="mt-4 max-w-md text-sm sm:text-base text-white/90">
                        Enterprise-grade payroll, attendance, and HR operations with a focused brand system.
                    </p>
                </div>
            </div>

            <div class="relative z-10 mt-12 sm:mt-16 lg:mt-0 flex items-end justify-between gap-6">
                <div class="space-y-2">
                        <p class="text-lg sm:text-xl font-semibold text-black dark:text-white">ENTERPRISE INC.</p>
                        <p class="max-w-sm text-sm text-white/80 dark:text-white/70">Secure access for payroll, HR, and employee self-service.</p>
                <div class="relative h-32 w-40 shrink-0">
                    <div class="absolute right-0 bottom-0 h-24 w-36 rounded-tr-3xl border-r-2 border-b-2 border-black/90"></div>
                    <div class="absolute right-8 bottom-10 h-3 w-3 rounded-full bg-black"></div>
                    <div class="absolute right-16 bottom-18 h-3 w-3 rounded-full bg-black"></div>
                    <div class="absolute right-24 bottom-26 h-3 w-3 rounded-full bg-black"></div>
                    <div class="absolute right-10 bottom-16 h-px w-14 bg-black"></div>
                    <div class="absolute right-18 bottom-24 h-px w-14 bg-black"></div>
                    <div class="absolute right-26 bottom-32 h-px w-14 bg-black"></div>
                </div>
            </div>
        </section>

        <section class="relative flex items-center justify-center bg-[#ED1C24] px-6 py-10 sm:px-8 lg:px-12 dark:bg-slate-950">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(255,255,255,0.2),_transparent_35%),radial-gradient(circle_at_bottom_left,_rgba(0,0,0,0.14),_transparent_30%)] dark:bg-[radial-gradient(circle_at_top_right,_rgba(255,255,255,0.08),_transparent_35%),radial-gradient(circle_at_bottom_left,_rgba(255,255,255,0.04),_transparent_30%)]"></div>

            <div class="relative z-10 w-full max-w-md">
                <div class="mb-6 sm:mb-8 text-center text-white">
                    <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-white shadow-xl shadow-black/20 dark:bg-slate-900">
                        <i class="fas fa-building text-2xl text-black"></i>
                    </div>
                    <h2 class="mt-4 text-2xl sm:text-3xl font-bold text-white">Sign in to your account</h2>
                    <p class="mt-2 text-sm text-white/85">Use your company credentials to continue.</p>
                </div>

                <div class="rounded-3xl border border-black/10 bg-white/95 p-6 sm:p-8 shadow-2xl backdrop-blur-xl dark:border-slate-700/60 dark:bg-slate-900/95 dark:text-slate-100">
                    <form method="POST" action="<?php echo e(route('login.post')); ?>" class="space-y-5">
                        <?php echo csrf_field(); ?>

                        <div>
                            <label for="email" class="block text-sm font-medium text-black mb-1.5 dark:text-slate-100">
                                <i class="fas fa-envelope mr-2 text-[#2D9344]"></i>Email Address
                            </label>
                            <div class="relative">
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="<?php echo e(old('email')); ?>"
                                    class="w-full rounded-xl border border-black/15 bg-white px-3 py-3 pl-10 text-sm text-black placeholder:text-black/35 focus:border-[#2D9344] focus:outline-none focus:ring-2 focus:ring-[#2D9344]/25 dark:border-slate-700/60 dark:bg-slate-900 dark:text-slate-100 dark:placeholder:text-slate-500 <?php echo e($errors->has('email') ? 'border-[#ED1C24]' : ''); ?>"
                                    placeholder="Enter your email"
                                    required
                                >
                                <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-[#2D9344] text-sm"></i>
                            </div>
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-2 flex items-center text-sm text-[#ED1C24]">
                                    <i class="fas fa-exclamation-circle mr-1"></i><?php echo e($message); ?>

                                </p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-black mb-1.5 dark:text-slate-100">
                                <i class="fas fa-lock mr-2 text-[#2D9344]"></i>Password
                            </label>
                            <div class="relative">
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    class="w-full rounded-xl border border-black/15 bg-white px-3 py-3 pl-10 pr-10 text-sm text-black placeholder:text-black/35 focus:border-[#2D9344] focus:outline-none focus:ring-2 focus:ring-[#2D9344]/25 dark:border-slate-700/60 dark:bg-slate-900 dark:text-slate-100 dark:placeholder:text-slate-500 <?php echo e($errors->has('password') ? 'border-[#ED1C24]' : ''); ?>"
                                    placeholder="Enter your password"
                                    required
                                >
                                <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-[#2D9344] text-sm"></i>
                                <button
                                    type="button"
                                    onclick="togglePassword('password')"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-black/60 transition-colors hover:text-[#2D9344] dark:text-slate-300 dark:hover:text-[#2D9344]"
                                >
                                    <i class="fas fa-eye text-sm" id="toggleIcon-password"></i>
                                </button>
                            </div>
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-2 flex items-center text-sm text-[#ED1C24]">
                                    <i class="fas fa-exclamation-circle mr-1"></i><?php echo e($message); ?>

                                </p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-black/20 text-[#2D9344] focus:ring-[#2D9344] dark:border-slate-600">
                                <span class="ml-2 text-sm text-black dark:text-slate-100">Remember me</span>
                            </label>
                            <a href="<?php echo e(route('password.request')); ?>" class="text-sm font-medium text-black hover:text-[#ED1C24] transition-colors dark:text-slate-100 dark:hover:text-[#ED1C24]">
                                Forgot password?
                            </a>
                        </div>

                        <button
                            type="submit"
                            class="w-full rounded-xl bg-[#2D9344] px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-black/15 transition-all duration-300 hover:bg-black hover:shadow-xl"
                        >
                            <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                        </button>

                        <div class="relative py-1">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-black/10 dark:border-slate-700/60"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="bg-white px-3 text-black/55 dark:bg-slate-900/95 dark:text-slate-400">Or continue with</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <button type="button" class="flex items-center justify-center rounded-xl border border-black/15 bg-white px-3 py-2.5 text-sm text-black transition-all duration-300 hover:border-[#2D9344] hover:text-[#2D9344] dark:border-slate-700/60 dark:bg-slate-800 dark:text-slate-100 dark:hover:text-[#2D9344]">
                                <i class="fab fa-google mr-2 text-[#ED1C24] text-sm"></i>Google
                            </button>
                            <button type="button" class="flex items-center justify-center rounded-xl border border-black/15 bg-white px-3 py-2.5 text-sm text-black transition-all duration-300 hover:border-[#2D9344] hover:text-[#2D9344] dark:border-slate-700/60 dark:bg-slate-800 dark:text-slate-100 dark:hover:text-[#2D9344]">
                                <i class="fab fa-microsoft mr-2 text-black text-sm dark:text-white"></i>Microsoft
                            </button>
                        </div>
                    </form>
                </div>

                <p class="mt-6 text-center text-xs text-white/80">© 2025 Enterprise Inc. All rights reserved.</p>
            </div>
        </section>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Gr8 tech new\GR8TECH\resources\views/auth/login.blade.php ENDPATH**/ ?>