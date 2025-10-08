<?php

use Illuminate\Support\Facades\Route;
use Kreait\Firebase\Contract\Auth;

Route::get('/debug-users', function (Auth $auth) {
    try {
        $users = $auth->listUsers();

        echo "<h2>Firebase Users Debug</h2>";
        echo "<style>table{border-collapse:collapse;width:100%} th,td{border:1px solid #ddd;padding:8px;text-align:left} th{background-color:#f2f2f2}</style>";
        echo "<table>";
        echo "<tr><th>Email</th><th>Display Name</th><th>UID</th><th>Created</th></tr>";

        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . ($user->email ?? 'N/A') . "</td>";
            echo "<td><strong>" . ($user->displayName ?? 'N/A') . "</strong></td>";
            echo "<td>" . $user->uid . "</td>";
            echo "<td>" . $user->metadata->createdAt->format('Y-m-d H:i:s') . "</td>";
            echo "</tr>";
        }

        echo "</table>";
    } catch (Exception $e) {
        return "Error: " . $e->getMessage();
    }
});
