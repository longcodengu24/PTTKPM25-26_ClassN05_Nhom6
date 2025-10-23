<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto">
    <h1 class="orbitron text-3xl font-bold text-white mb-8 text-center drop-shadow">Cài Đặt Tài Khoản</h1>
    
    <!-- Success/Error Messages -->
    <?php if(session('success')): ?>
        <div class="bg-green-500/80 backdrop-blur-sm text-white p-4 rounded-xl mb-6 border border-green-400/50">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <?php echo e(session('success')); ?>

            </div>
        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="bg-red-500/80 backdrop-blur-sm text-white p-4 rounded-xl mb-6 border border-red-400/50">
            <div class="flex items-start">
                <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <div class="font-semibold mb-1">Có lỗi xảy ra:</div>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="mb-1">• <?php echo e($error); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('account.update')); ?>" method="POST" enctype="multipart/form-data" class="bg-white/30 backdrop-blur-lg rounded-2xl shadow-xl p-8 space-y-6 border border-white/20">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        
        <!-- Current Avatar Display -->
        <div class="text-center mb-6">
            <img src="<?php echo e(isset($currentUser) ? $currentUser['avatar'] : (isset($userData) ? $userData['avatar'] : session('avatar', '/img/default-avatar.png'))); ?>" 
                 alt="Avatar" 
                 id="current-avatar"
                 class="w-24 h-24 rounded-full mx-auto border-4 border-white/50 shadow-lg">
        </div>

        <div>
            <label class="block text-gray-800 font-semibold mb-2" for="name">Tên đăng nhập</label>
            <input type="text" id="name" name="name" 
                   value="<?php echo e(old('name', isset($currentUser) ? $currentUser['name'] : (isset($userData) ? $userData['name'] : session('name', '')))); ?>" 
                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 outline-none bg-white/80"
                   required />
        </div>

        <div>
            <label class="block text-gray-800 font-semibold mb-2" for="email">Email (chỉ đọc)</label>
            <input type="email" id="email" 
                   value="<?php echo e(isset($currentUser) ? $currentUser['email'] : (isset($userData) ? $userData['email'] : session('email', ''))); ?>" 
                   class="w-full px-4 py-2 rounded-lg border border-gray-300 bg-gray-100 text-gray-600"
                   readonly />
        </div>

        <div>
            <label class="block text-gray-800 font-semibold mb-2" for="avatar">Ảnh đại diện</label>
            <input type="file" id="avatar" name="avatar" 
                   class="w-full text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" 
                   accept="image/jpeg,image/png,image/jpg,image/gif" 
                   onchange="validateFile(this)" />
            <p class="text-sm text-gray-600 mt-1">Chấp nhận: JPG, JPEG, PNG, GIF. Tối đa 2MB.</p>
            
            <!-- Preview avatar -->
            <div class="mt-3">
                <img id="avatar-preview" src="" alt="Preview" class="w-20 h-20 rounded-full border-2 border-gray-300 hidden">
            </div>
            
            <!-- Upload status -->
            <div id="upload-status" class="mt-2 hidden">
                <div class="text-sm text-blue-600">📤 Đang chuẩn bị upload...</div>
            </div>
        </div>

        <script>
            // Validate file function
            function validateFile(input) {
                const file = input.files[0];
                const preview = document.getElementById('avatar-preview');
                const currentAvatar = document.getElementById('current-avatar');
                const status = document.getElementById('upload-status');
                
                // Reset
                preview.classList.add('hidden');
                status.classList.add('hidden');
                
                if (!file) return;
                
                console.log('File selected:', {
                    name: file.name,
                    size: file.size,
                    type: file.type
                });
                
                // Kiểm tra kích thước file (2MB = 2 * 1024 * 1024 bytes)
                if (file.size > 2097152) {
                    alert('File quá lớn! Vui lòng chọn file nhỏ hơn 2MB.');
                    input.value = '';
                    return false;
                }
                
                // Kiểm tra định dạng file
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Định dạng file không được hỗ trợ! Vui lòng chọn file JPG, PNG hoặc GIF.');
                    input.value = '';
                    return false;
                }
                
                // Show preview và cập nhật avatar hiện tại
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    status.classList.remove('hidden');
                    
                    // Cập nhật avatar hiện tại luôn để user thấy trước
                    currentAvatar.src = e.target.result;
                };
                reader.readAsDataURL(file);
                
                return true;
            }
            
            // Preview avatar trước khi upload
            document.getElementById('avatar').addEventListener('change', function(e) {
                validateFile(this);
            });
            
            // Debug form submission
            document.querySelector('form').addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                const avatarFile = document.getElementById('avatar').files[0];
                
                // Disable submit button to prevent double submission
                submitBtn.disabled = true;
                submitBtn.innerHTML = '⏳ Đang xử lý...';
                
                const formData = new FormData(this);
                console.log('Form submission data:');
                for (let [key, value] of formData.entries()) {
                    if (value instanceof File) {
                        console.log(key + ':', {
                            name: value.name,
                            size: value.size,
                            type: value.type
                        });
                    } else {
                        console.log(key + ':', value);
                    }
                }
                
                // Re-enable button after 3 seconds (in case of server error)
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '💾 Lưu thay đổi';
                }, 3000);
            });
        </script>

        <div>
            <label class="block text-gray-800 font-semibold mb-2" for="password">Mật khẩu mới (để trống nếu không đổi)</label>
            <input type="password" id="password" name="password" 
                   placeholder="••••••••" 
                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 outline-none bg-white/80" />
        </div>

        <div>
            <label class="block text-gray-800 font-semibold mb-2" for="password_confirmation">Nhập lại mật khẩu mới</label>
            <input type="password" id="password_confirmation" name="password_confirmation" 
                   placeholder="••••••••" 
                   class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 outline-none bg-white/80" />
        </div>

        <div class="flex justify-end gap-4 pt-4">
            <a href="<?php echo e(route('account.index')); ?>" 
               class="px-5 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 transition">
                Hủy
            </a>
            <button type="submit" 
                    class="px-5 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition shadow">
                💾 Lưu thay đổi
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.account', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\app\laragon\www\cayvlon\PTTKPM25-26_ClassN05_Nhom6\SRC\SM_Store\resources\views/account/settings.blade.php ENDPATH**/ ?>