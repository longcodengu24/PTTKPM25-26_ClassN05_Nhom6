// Global variable
var currentTransactionId = null;
var selectedAmount = null;
var autoCheckInterval = null; // Interval cho auto-check
var initialCoins = 0; // Lưu số coins ban đầu để so sánh

document.addEventListener("DOMContentLoaded", function () {
    var customAmountInput = document.getElementById("customAmount");
    var amountBtns = document.querySelectorAll(".amount-btn");
    var submitBtn = document.getElementById("submitBtn");

    console.log("✅ Deposit page loaded");
    console.log("Found buttons:", amountBtns.length);

    // Click vào nút gói nạp
    for (var i = 0; i < amountBtns.length; i++) {
        amountBtns[i].onclick = function () {
            var amount = parseInt(this.getAttribute("data-amount"));
            selectedAmount = amount;
            customAmountInput.value = amount.toLocaleString("vi-VN");

            console.log("Selected amount:", amount);

            // Reset tất cả nút
            for (var j = 0; j < amountBtns.length; j++) {
                amountBtns[j].classList.remove(
                    "bg-yellow-200",
                    "border-yellow-400",
                    "shadow-lg"
                );
                amountBtns[j].classList.add("bg-white/60", "border-yellow-200");
            }

            // Highlight nút được chọn
            this.classList.remove("bg-white/60", "border-yellow-200");
            this.classList.add(
                "bg-yellow-200",
                "border-yellow-400",
                "shadow-lg"
            );
        };
    }

    // Click nút nạp tiền
    submitBtn.onclick = function () {
        console.log("🚀 Submit button clicked!");

        var amount =
            selectedAmount ||
            parseInt(customAmountInput.value.replace(/,/g, ""));

        if (!amount || amount < 10000) {
            alert("Vui lòng chọn hoặc nhập số tiền (tối thiểu 10,000đ)");
            return;
        }

        console.log("Amount to deposit:", amount);

        // Lấy coins hiện tại trước khi nạp
        fetch("/api/payment/status", {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (statusData) {
                if (statusData.success) {
                    initialCoins = statusData.coins || 0;
                    console.log("💰 Initial coins:", initialCoins);
                }

                // Sau đó mới tạo deposit request
                return fetch("/api/payment/create", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    body: JSON.stringify({
                        amount: amount,
                        user_id: document.getElementById("user_id").value,
                    }),
                });
            })
            .then(function (response) {
                console.log("Response status:", response.status);
                return response.json();
            })
            .then(function (data) {
                console.log("Response data:", data);

                if (data.success) {
                    showPaymentModal(data.data);
                } else {
                    alert("❌ Error: " + data.message);
                }
            })
            .catch(function (error) {
                console.error("Error:", error);
                alert("Có lỗi xảy ra: " + error.message);
            });
    };
});

// Function to show payment modal with QR code
function showPaymentModal(data) {
    console.log("🎯 Showing payment modal:", data);

    var modal = document.getElementById("paymentModal");
    var paymentInfo = document.getElementById("paymentInfo");

    if (!modal || !paymentInfo) {
        console.error("Modal elements not found!");
        return;
    }

    // Build HTML content
    var html = '<div class="text-left">';

    // Mã giao dịch - không cần hiển thị nữa vì không có transaction
    // html += '<div class="bg-blue-50 p-4 rounded-lg mb-4">';
    // html += '<p class="text-sm text-gray-600 mb-1">Mã giao dịch:</p>';
    // html += '<p class="font-bold text-blue-600">' + data.transaction_id + "</p>";
    // html += "</div>";

    // Amount
    html += '<div class="bg-green-50 p-4 rounded-lg mb-4">';
    html += '<p class="text-sm text-gray-600 mb-1">Số tiền nạp:</p>';
    html +=
        '<p class="font-bold text-green-600 text-lg">' +
        parseInt(data.amount).toLocaleString("vi-VN") +
        "đ</p>";
    html +=
        '<p class="text-xs text-gray-500">= ' +
        parseInt(data.amount).toLocaleString("vi-VN") +
        " coins</p>";
    html += "</div>";

    // QR Code
    var qrUrl = data.qr_code || data.qr_url; // Support both field names
    if (qrUrl) {
        html +=
            '<div class="text-center mb-4 bg-white p-6 rounded-lg shadow-inner">';
        html +=
            '<h3 class="text-lg font-bold text-gray-800 mb-4">🎯 Quét mã QR để thanh toán</h3>';
        html += '<div class="qr-container flex justify-center">';
        html +=
            '<img src="' +
            qrUrl +
            '" alt="QR Code" class="w-80 h-80 border-4 border-blue-500 rounded-xl shadow-2xl" onerror="this.onerror=null; this.src=\'https://via.placeholder.com/320x320.png?text=QR+Loading...\';">';
        html += "</div>";
        html +=
            '<p class="text-sm text-blue-600 mt-4 font-semibold">📱 Mở app ngân hàng → Quét QR → Xác nhận</p>';
        html +=
            '<p class="text-xs text-gray-500 mt-2">Giao dịch được xử lý tự động sau khi chuyển khoản</p>';
        html += "</div>";
    } else {
        html +=
            '<div class="bg-red-50 p-4 rounded-lg mb-4 border border-red-200">';
        html +=
            '<p class="text-red-600 font-semibold">⚠️ Không thể tạo mã QR</p>';
        html +=
            '<p class="text-sm text-red-500">Vui lòng chuyển khoản thủ công theo thông tin bên dưới</p>';
        html += "</div>";
    }

    // Bank info
    if (data.bank_info) {
        html +=
            '<div class="bg-yellow-50 p-4 rounded-lg text-sm border-2 border-yellow-400">';
        html +=
            '<h4 class="font-semibold mb-3 text-yellow-800 text-base">🏦 Hoặc chuyển khoản thủ công:</h4>';

        if (data.bank_info.bank_name) {
            html +=
                '<p class="mb-1">Ngân hàng: <strong>' +
                data.bank_info.bank_name +
                "</strong></p>";
        }
        if (data.bank_info.account_number) {
            html +=
                '<p class="mb-1">Số TK: <strong>' +
                data.bank_info.account_number +
                "</strong></p>";
        }
        if (data.bank_info.account_name) {
            html +=
                '<p class="mb-3">Tên TK: <strong>' +
                data.bank_info.account_name +
                "</strong></p>";
        }

        // Nội dung chuyển khoản (quan trọng nhất)
        if (data.bank_info.content) {
            html +=
                '<div class="bg-red-50 border-2 border-red-500 p-3 rounded-lg mt-3">';
            html +=
                '<p class="text-red-800 font-bold text-sm mb-1">⚠️ NỘI DUNG CHUYỂN KHOẢN:</p>';
            html +=
                '<p class="text-red-600 font-mono text-lg font-bold text-center bg-white p-2 rounded border border-red-300">' +
                data.bank_info.content +
                "</p>";
            html +=
                '<p class="text-xs text-red-700 mt-2 font-semibold">📝 Copy chính xác nội dung này khi chuyển khoản</p>';
            html += "</div>";
        }

        html +=
            '<p class="text-xs text-yellow-800 mt-3 font-semibold bg-yellow-100 p-2 rounded">⚡ Hệ thống tự động cộng coins khi nhận được chuyển khoản đúng nội dung</p>';
        html += "</div>";
    }

    html += "</div>";

    // Set HTML and show modal
    paymentInfo.innerHTML = html;
    modal.classList.remove("hidden");

    console.log("✅ Modal displayed!");

    // Bắt đầu auto-check mỗi 10 giây
    startAutoCheck();
}

// Tự động kiểm tra trạng thái thanh toán
function startAutoCheck() {
    // Clear interval cũ nếu có
    if (autoCheckInterval) {
        clearInterval(autoCheckInterval);
    }

    console.log("🔄 Starting auto-check every 10 seconds...");

    autoCheckInterval = setInterval(function () {
        console.log("🔍 Auto-checking payment status...");

        fetch("/api/payment/status", {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                console.log("Auto-check response:", data);

                if (data.success) {
                    var currentCoins = data.coins || 0;
                    console.log(
                        "Comparing: current=" +
                            currentCoins +
                            " vs initial=" +
                            initialCoins
                    );

                    // Nếu coins tăng = đã nhận tiền
                    if (currentCoins > initialCoins) {
                        var addedCoins = currentCoins - initialCoins;

                        // Stop auto-check
                        clearInterval(autoCheckInterval);
                        autoCheckInterval = null;

                        console.log(
                            "✅ Payment completed! Added " +
                                addedCoins +
                                " coins. Auto-reload..."
                        );

                        // Đóng modal
                        var modal = document.getElementById("paymentModal");
                        if (modal) {
                            modal.classList.add("hidden");
                        }

                        // Show alert và reload
                        alert(
                            "✅ Thanh toán thành công!\n💰 Đã cộng " +
                                addedCoins.toLocaleString("vi-VN") +
                                " coins vào tài khoản.\n\nTrang sẽ được tải lại."
                        );

                        window.location.reload();
                    } else {
                        console.log(
                            "⏳ Waiting for payment... (coins unchanged)"
                        );
                    }
                }
            })
            .catch(function (error) {
                console.error("Auto-check error:", error);
            });
    }, 10000); // Check every 10 seconds
}

// Dừng auto-check khi đóng modal
function stopAutoCheck() {
    if (autoCheckInterval) {
        clearInterval(autoCheckInterval);
        autoCheckInterval = null;
        console.log("⏹️ Stopped auto-check");
    }
}

// Check payment status function - So sánh coins để biết đã nhận tiền chưa
function checkPaymentStatus() {
    console.log("🔍 Checking payment status (comparing coins)");
    console.log("Initial coins:", initialCoins);

    var checkBtn = document.getElementById("checkPaymentBtn");
    if (checkBtn) {
        checkBtn.disabled = true;
        checkBtn.textContent = "Đang kiểm tra...";
    }

    fetch("/api/payment/status", {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
        },
    })
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            console.log("Status response:", data);
            console.log("Current coins:", data.coins);
            console.log("Initial coins:", initialCoins);

            if (checkBtn) {
                checkBtn.disabled = false;
                checkBtn.textContent = "🔄 Kiểm tra thanh toán";
            }

            if (data.success) {
                var currentCoins = data.coins || 0;

                // So sánh: nếu coins tăng = đã nhận tiền
                if (currentCoins > initialCoins) {
                    var addedCoins = currentCoins - initialCoins;
                    console.log("✅ Payment completed! Added:", addedCoins);

                    // Dừng auto-check
                    stopAutoCheck();

                    // Đóng modal
                    var modal = document.getElementById("paymentModal");
                    if (modal) {
                        modal.classList.add("hidden");
                    }

                    // Hiển thị thông báo và reload
                    alert(
                        "✅ Thanh toán thành công!\n💰 Đã cộng " +
                            addedCoins.toLocaleString("vi-VN") +
                            " coins vào tài khoản.\n\nTrang sẽ được tải lại."
                    );

                    // Reload
                    window.location.reload();
                } else {
                    console.log("⏳ No payment received yet");
                    alert(
                        "⏳ Chưa nhận được thanh toán.\n\nVui lòng quét mã QR hoặc chuyển khoản theo thông tin đã cung cấp."
                    );
                }
            } else {
                alert(
                    "❌ Không thể kiểm tra trạng thái.\n" + (data.message || "")
                );
            }
        })
        .catch(function (error) {
            console.error("Error:", error);
            if (checkBtn) {
                checkBtn.disabled = false;
                checkBtn.textContent = "🔄 Kiểm tra thanh toán";
            }
            alert("Có lỗi xảy ra khi kiểm tra: " + error.message);
        });
}

// Close modal button handler
document.addEventListener("DOMContentLoaded", function () {
    var closeBtn = document.getElementById("closeModalBtn");
    var checkBtn = document.getElementById("checkPaymentBtn");
    var modal = document.getElementById("paymentModal");

    if (closeBtn && modal) {
        closeBtn.onclick = function () {
            stopAutoCheck(); // Dừng auto-check khi đóng modal
            modal.classList.add("hidden");
        };

        // Close when clicking outside
        modal.onclick = function (e) {
            if (e.target === modal) {
                stopAutoCheck(); // Dừng auto-check khi đóng modal
                modal.classList.add("hidden");
            }
        };
    }

    if (checkBtn) {
        checkBtn.onclick = checkPaymentStatus;
    }
});
