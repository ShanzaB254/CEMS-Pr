<?php
// index.php
require_once 'includes/manage-db/db_connect.php';

// 1. Fetch the 3 most recent active announcements
$announcements_sql = "SELECT title, message, created_at FROM announcements ORDER BY created_at DESC LIMIT 3";
$announcements_result = $conn->query($announcements_sql);

// 2. Fetch approved upcoming events for the public calendar
$calendar_sql = "SELECT e.id, e.title, e.event_date, e.event_time, e.venue, e.description, u.name AS organizer_name 
                 FROM events e 
                 JOIN users u ON e.organizer_id = u.id 
                 WHERE e.status = 'approved' AND e.event_date >= CURDATE() 
                 ORDER BY e.event_date ASC LIMIT 6";
$calendar_result = $conn->query($calendar_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Events - Home</title>
    <link rel="stylesheet" href="assets/css/common.css">
    <link rel="stylesheet" href="assets/css/public.css">
</head>
<body>

    <header class="public-header">
        <a href="index.php" class="brand">
        <img src="assets/images/logo.webp" alt="CEMS Logo" style="height: 80px; width: 80px; border-radius: 8px;">    
        CEMS</a>
        <nav class="public-nav">
            <a href="index.php">Home</a>
            <a href="#events">Browse Events</a>
            <a href="public/login.php" class="btn-nav">Login</a>
            <a href="public/register.php" class="btn btn-primary" style="padding: 8px 16px; color:white;">Register</a>
        </nav>
    </header>

    <section class="hero-section">
        <h1>Discover Campus Life</h1>
        <p>Your centralized hub for university workshops, seminars, volunteer opportunities, and student activities.</p>
        <div style="display: flex; gap: 15px; justify-content: center;">
            <a href="#events" class="btn" style="background: white; color: var(--primary);">View Calendar</a>
            <a href="public/register.php" class="btn" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.4);">Join the Platform</a>
        </div>
    </section>

    <main class="container">
        
        <?php if ($announcements_result->num_rows > 0): ?>
            <section style="margin-bottom: 60px;">
                <h2 style="font-size: 1.5rem; color: var(--text-dark); margin-bottom: 20px; border-bottom: none;">
                <span >
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-megaphone-icon lucide-megaphone"><path d="M11 6a13 13 0 0 0 8.4-2.8A1 1 0 0 1 21 4v12a1 1 0 0 1-1.6.8A13 13 0 0 0 11 14H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z"/><path d="M6 14a12 12 0 0 0 2.4 7.2 2 2 0 0 0 3.2-2.4A8 8 0 0 1 10 14"/><path d="M8 6v8"/></svg>
                </span>    
                Campus Broadcasts</h2>
                <?php while($news = $announcements_result->fetch_assoc()): ?>
                    <div class="announcement-banner">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <h3 style="margin: 0; color: var(--text-dark); font-size: 1.2rem;"><?php echo htmlspecialchars($news['title']); ?></h3>
                            <span style="font-size: 0.85rem; color: var(--text-muted);"><?php echo date('F d, Y', strtotime($news['created_at'])); ?></span>
                        </div>
                        <p style="margin: 0; color: var(--text-dark);"><?php echo nl2br(htmlspecialchars($news['message'])); ?></p>
                    </div>
                <?php endwhile; ?>
            </section>
        <?php endif; ?>

        <section id="events">
            <h2 style="font-size: 1.75rem; color: var(--text-dark); margin-bottom: 10px; border-bottom: none;">Upcoming Events</h2>
            <p style="color: var(--text-muted); margin-bottom: 30px;">Log in to secure your spot or volunteer for these activities.</p>

            <div class="event-grid">
                <?php if ($calendar_result->num_rows > 0): ?>
                    <?php while($event = $calendar_result->fetch_assoc()): ?>
                        <div class="event-card">
                            <h3 style="color: #1e3a8a; margin: 0; font-size: 1.25rem;"><?php echo htmlspecialchars($event['title']); ?></h3>
                            
                            <div class="event-meta">
                                
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-days-icon lucide-calendar-days"><path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/></svg>
                            </span><strong><?php echo date('M d, Y', strtotime($event['event_date'])); ?></strong> at <?php echo date('h:i A', strtotime($event['event_time'])); ?><br>
                                
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin-icon lucide-map-pin"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
                            </span><?php echo htmlspecialchars($event['venue']); ?><br>
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-round-pen-icon lucide-user-round-pen"><path d="M2 21a8 8 0 0 1 10.821-7.487"/><path d="M21.378 16.626a1 1 0 0 0-3.004-3.004l-4.01 4.012a2 2 0 0 0-.506.854l-.837 2.87a.5.5 0 0 0 .62.62l2.87-.837a2 2 0 0 0 .854-.506z"/><circle cx="10" cy="8" r="5"/></svg>
                                </span> By: <?php echo htmlspecialchars($event['organizer_name']); ?>
                            </div>
                            
                            <div style="color: var(--text-dark); font-size: 0.95rem; margin-bottom: 20px; flex: 1;">
                                <?php echo htmlspecialchars(substr($event['description'], 0, 100)) . '...'; ?>
                            </div>
                            
                            <a href="public/login.php" class="btn" style="background: #e0e7ff; color: #4338ca; width: 100%;">Login to Register</a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="grid-column: 1 / -1; padding: 60px 20px; text-align: center; background: white; border-radius: 12px; border: 2px dashed var(--border-color);">
                        <h3 style="color: var(--text-muted); margin-bottom: 10px;">The calendar is currently clear.</h3>
                        <p style="color: var(--text-muted); margin: 0;">Check back later for new events, or register to stay updated!</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

    </main>

    <footer style="background: #0f172a; color: #94a3b8; text-align: center; padding: 30px; margin-top: auto;">
        <p style="margin: 0;">&copy; <?php echo date('Y'); ?> Campus Event Management System. All rights reserved.</p>
    </footer>

</body>
</html>