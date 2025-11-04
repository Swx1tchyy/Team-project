<?php
$host = "mysql";
$user = "root";
$pass = "password";
$dbname = "teamProject";
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $naam = $_POST['fname'] ?? '';
    $beschrijving = $_POST['beschrijving'] ?? '';
    $tijd = $_POST['tijd'] ?? '';

    if (!empty($naam) && !empty($beschrijving) && !empty($tijd)) {
        $stmt = $conn->prepare("INSERT INTO planning (naam, beschrijving, tijd) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $naam, $beschrijving, $tijd);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();

        echo json_encode([
            "id" => $id,
            "naam" => $naam,
            "beschrijving" => $beschrijving,
            "tijd" => $tijd
        ]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM planning WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        echo json_encode(["success" => true]);
    }
    exit;
}

$result = $conn->query("SELECT * FROM planning ORDER BY id DESC");
$tickets = [];
while ($row = $result->fetch_assoc()) {
    $tickets[] = $row;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="nl-NL">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="planningPage.css">
    <title>Plannen</title>
    <style>
        .ticket {
            background: #f2f2f2;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 10px;
            position: relative;
        }
        .delete-btn {
            position: absolute;
            right: 10px;
            top: 10px;
            background: #e74c3c;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            padding: 4px 8px;
            font-size: 12px;
        }
        .delete-btn:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>
    <h1>Tickets voor vak</h1>

    <div class="container">
        <div class="left-side" id="ticketList">
            <?php if (empty($tickets)): ?>
                <p class="placeholder">Nog geen tickets toegevoegd...</p>
            <?php else: ?>
                <?php foreach ($tickets as $t): ?>
                    <div class="ticket" data-id="<?= $t['id'] ?>">
                        <button class="delete-btn">Verwijderen</button>
                        <h3><?= htmlspecialchars($t['naam']) ?></h3>
                        <p><strong>Beschrijving:</strong> <?= htmlspecialchars($t['beschrijving']) ?></p>
                        <p><strong>Tijd:</strong> <?= htmlspecialchars($t['tijd']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="right-side">
            <form id="ticketForm">
                <label for="fname">Planning naam:</label>
                <input type="text" id="fname" name="fname" required>

                <label for="beschrijving">Beschrijving:</label>
                <input type="text" id="beschrijving" name="beschrijving" required>

                <label for="tijd">Tijds uur:</label>
                <input type="text" id="tijd" name="tijd" required>

                <input type="submit" value="Toevoegen">
            </form>
        </div>
    </div>

    <script>
    const form = document.getElementById('ticketForm');
    const ticketList = document.getElementById('ticketList');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const name = document.getElementById('fname').value.trim();
        const beschrijving = document.getElementById('beschrijving').value.trim();
        const tijd = document.getElementById('tijd').value.trim();
        if (!name || !beschrijving || !tijd) return;

        const response = await fetch('', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'add',
                fname: name,
                beschrijving: beschrijving,
                tijd: tijd
            })
        });
        const data = await response.json();

        const placeholder = document.querySelector('.placeholder');
        if (placeholder) placeholder.remove();

        const ticket = document.createElement('div');
        ticket.classList.add('ticket');
        ticket.dataset.id = data.id;
        ticket.innerHTML = `
            <button class="delete-btn">Verwijderen</button>
            <h3>${data.naam}</h3>
            <p><strong>Beschrijving:</strong> ${data.beschrijving}</p>
            <p><strong>Tijd:</strong> ${data.tijd}</p>
        `;
        ticketList.prepend(ticket);

        form.reset();
    });

    ticketList.addEventListener('click', async (e) => {
        if (!e.target.classList.contains('delete-btn')) return;

        const ticket = e.target.closest('.ticket');
        const id = ticket.dataset.id;

        if (!confirm('Weet je zeker dat je dit ticket wilt verwijderen?')) return;

        const response = await fetch('', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'delete', id })
        });

        const result = await response.json();
        if (result.success) {
            ticket.remove();
           
            if (ticketList.children.length === 0) {
                ticketList.innerHTML = '<p class="placeholder">Nog geen tickets toegevoegd...</p>';
            }
        }
    });
    </script>
</body>
</html>
