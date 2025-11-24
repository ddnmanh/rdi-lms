<!DOCTYPE html>
<html lang="vi" class="h-full">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Đăng nhập - Admin</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    

    <style>
        html,
        body,
        main {
            overflow-x: hidden;
            max-width: 100%;
            font-size: 16px;
            color: #314158;
            font-family: 'UTM Avo', sans-serif;
        }

        @media (min-width: 1536px) {
            html,
            body,
            main {
                font-size: 18px;
            }
        }
    </style>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brandBlue: '#2563eb',
                        brandGreen: '#10b981'
                    },
                    keyframes: {
                        spinfast: {
                            to: { transform: "rotate(360deg)" }
                        }
                    },
                    animation: {
                        spinfast: "spinfast .8s linear infinite"
                    }
                }
            }
        };
    </script>
</head>

<body class="min-h-screen flex items-center justify-center p-8
    bg-[radial-gradient(circle_at_10%_20%,rgba(37,99,235,0.25),transparent_40%),radial-gradient(circle_at_90%_0%,rgba(16,185,129,0.35),transparent_55%),linear-gradient(135deg,#f0f9ff,#ecfdf5)]
    dark:bg-[#0f172a]
">

    <div class="w-full max-w-[450px] relative flex flex-col items-stretch justify-center gap-4"> 

        <!-- Login Card -->
        <div class="bg-white dark:bg-gray-800 border border-blue-500/30 dark:border-blue-500/40
            rounded-3xl shadow-[0_30px_60px_rgba(15,23,42,0.15)]
            pt-8 pb-8 px-8 flex flex-col items-stretch justify-start
        ">

            <h3 class="mb-4 w-full text-center text-blue-700 dark:text-blue-400 font-bold text-lg">Học Tập Online</h3>

            <div class="mb-4 flex flex-col items-center justify-start">
                <h1 class="text-2xl font-extrabold text-center text-blue-700 dark:text-blue-400">
                    Chào mừng quản trị viên!
                </h1>
                <p class="text-center text-gray-500 dark:text-gray-300 text-sm">
                    Đăng nhập để theo dõi và quản lý hệ thống LMS của bạn.
                </p>
            </div>

            <form id="loginForm" class="space-y-4">

                <!-- Email -->
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] ml-4 font-semibold text-gray-700 dark:text-gray-200">
                        Email công việc
                    </label>
                    <input
                        type="email"
                        id="email"
                        required
                        placeholder="ten.ban@tencongty.com"
                        class="px-4 py-3 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                    >
                </div>

                <!-- Password -->
                <div class="flex flex-col gap-1">
                    <label class="text-[13px] ml-4 font-semibold text-gray-700 dark:text-gray-200">
                        Mật khẩu
                    </label>
                    <input
                        type="password"
                        id="password"
                        required
                        placeholder="Nhập mật khẩu của bạn"
                        class="px-4 py-3 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
                    >
                </div>

                <!-- Button -->
                <button id="submitBtn" type="submit"
                    class="w-full inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-green-500 hover:from-blue-700 hover:to-green-600 disabled:opacity-60 text-white font-bold px-4 py-3 transition-all shadow-lg"
                >
                    <span id="btnSubmitText">Đăng nhập</span>
                    <div id="loading" class="hidden flex items-center justify-center gap-2 text-sm text-white mt-3">
                        <div class="w-4 h-4 border-2 border-gray-300 border-t-brandBlue rounded-full animate-spinfast"></div>
                        <span>Đang xác thực...</span>
                    </div>
                </button>

            </form>

            <div class="mt-6 text-center text-[12px] text-gray-400 dark:text-gray-500">
                © <span id="currentYear"></span> LMS. Hỗ trợ:
                <a class="underline" href="mailto:support@lms.vn">support@lms.vn</a>
            </div>
        </div>

        <!-- Alert -->
        <div id="alertContainer" class="space-y-2"></div>

    </div>

    <!-- SCRIPT -->
    <script>
        const alertContainer = document.getElementById('alertContainer');
        const loginForm = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        const loading = document.getElementById('loading');
        const btnSubmitText = document.getElementById('btnSubmitText');

        function showAlert(msg, type = "error") {
            const classes = type === "error"
                ? "bg-red-100 border border-red-300 text-red-700"
                : "bg-green-100 border border-green-300 text-green-700";

            alertContainer.innerHTML = `
                <div class="p-3 rounded-xl text-sm ${classes}">
                    ${msg}
                </div>
            `;

            setTimeout(() => alertContainer.innerHTML = "", 5000);
        }

        function toggleLoading(isLoading) {
            submitBtn.disabled = isLoading;
            loading.classList.toggle("hidden", !isLoading);
            btnSubmitText.classList.toggle("hidden", isLoading);
        }

        loginForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            toggleLoading(true);

            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value;

            if (!email || !password) {
                showAlert("Vui lòng nhập đầy đủ thông tin.");
                toggleLoading(false);
                return;
            }

            try {
                const res = await fetch("/api/auth/login", {
                    method: "POST",
                    headers: { "Content-Type": "application/json", "Accept": "application/json" },
                    credentials: "include",
                    body: JSON.stringify({ email, password }),
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    showAlert("Đăng nhập thành công! Đang chuyển hướng...", "success"); 
                    setTimeout(() => {
                        window.location.href = "/admin/";
                    }, 600);
                } else {
                    showAlert(data.message || "Đăng nhập thất bại. Vui lòng thử lại.");
                }
            } catch (err) {
                showAlert("Không thể kết nối tới máy chủ.");
            }

            toggleLoading(false);
        });

        async function handleCheckLoggedIn() {
            try {
                const res = await fetch("/api/auth/me", {
                    method: "GET",
                    headers: { "Accept": "application/json" },
                    credentials: "include",
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    window.location.href = "/admin/";
                }
            } catch (err) {
                console.error("Lỗi kiểm tra đăng nhập:", err);
            }
        }

        document.addEventListener("DOMContentLoaded", async () => {
            await handleCheckLoggedIn();
            document.getElementById("currentYear").textContent = new Date().getFullYear();
        });
    </script>
</body>
</html>
