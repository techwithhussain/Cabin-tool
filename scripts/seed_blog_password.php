<?php

/**
 * Blog Seeder – "How to Share Passwords Securely Without Email or WhatsApp"
 * Run from project root: php scripts/seed_blog_password.php
 */

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH',  BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('STORAGE_PATH', BASE_PATH . '/storage');
define('CONFIG_PATH', BASE_PATH . '/config');

// Load autoloader
$autoloader = BASE_PATH . '/vendor/autoload.php';
if (file_exists($autoloader)) {
    require_once $autoloader;
} else {
    spl_autoload_register(function (string $class): void {
        $prefix = 'App\\';
        if (str_starts_with($class, $prefix)) {
            $relativeClass = substr($class, strlen($prefix));
            $file = APP_PATH . '/' . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        }
    });
}

use App\Core\Config;
use App\Core\Database;
use App\Repositories\BlogRepository;

// Bootstrap config (loads .env and defines APP_DEBUG constant)
Config::load();

// ─── Blog Content HTML ────────────────────────────────────────────────────────

$content = <<<'HTML'
<p>You need to send a password to a coworker. Or maybe a Wi-Fi key for a guest, an API token for a developer, or a login for a shared account. Without thinking much about it, you type it out in an email or a WhatsApp message and hit send.</p>

<figure class="article-img-figure">
  <img src="/assets/img/share password securely online.webp"
       alt="Securely sharing a password online without email or WhatsApp"
       class="article-img article-img--hero"
       loading="lazy" width="1200" height="630" />
  <figcaption>Securely sharing a password online without email or WhatsApp</figcaption>
</figure>

<p>A bad move. Because neither email nor WhatsApp were designed to store sensitive information in a safe place. Once it is out from your finger through either one of those apps it sits there readable and searchable, long after you need it to be seen.</p>

<h2>Why Email Is a Bad Place for Passwords</h2>

<p>Email feels private because it's addressed to one person. But behind the scenes:</p>

<figure class="article-img-figure">
  <img src="/assets/img/Securely sharing.webp"
       alt="Risks of sharing passwords through email including storage, search and data breaches"
       class="article-img"
       loading="lazy" width="1200" height="630" />
  <figcaption>Risks of sharing passwords through email including storage, search and data breaches</figcaption>
</figure>

<ul>
  <li><strong>It's stored forever.</strong> Most inboxes never get cleared. That password you sent two years ago is probably still sitting in a "Sent" folder somewhere.</li>
  <li><strong>It's searchable.</strong> Anyone with access to the inbox — an IT admin, a hacker, a nosy family member — can search for words like "password" and find it instantly.</li>
  <li><strong>It's backed up.</strong> Emails get synced across devices, backed up to the cloud, and sometimes even printed. You lose control the moment you hit send.</li>
  <li><strong>Phishing and breaches are common.</strong> If either party's email account is ever compromised, every password ever sent through it is compromised too.</li>
</ul>

<h2>Why WhatsApp Isn't Much Better</h2>

<p>WhatsApp's end-to-end encryption protects messages in transit, but that's where the protection ends.</p>

<figure class="article-img-figure">
  <img src="/assets/img/WhatsApp password sharing risks.webp"
       alt="WhatsApp password sharing risks from chat history, backups and screenshots"
       class="article-img"
       loading="lazy" width="1200" height="630" />
  <figcaption>WhatsApp password sharing risks from chat history, backups and screenshots</figcaption>
</figure>

<ul>
  <li><strong>Messages stay on the device.</strong> Chat backups (often unencrypted on Google Drive or iCloud) store your password in plain text.</li>
  <li><strong>Group chats make it worse.</strong> Anyone who was ever added to that group — even if removed later — may have already seen or downloaded the message.</li>
  <li><strong>No real deletion.</strong> "Delete for everyone" doesn't guarantee the recipient hasn't already screenshotted or backed up the chat.</li>
</ul>

<p>In short: convenient messaging apps are designed to keep information around. Sharing a secret is the opposite goal — you want it seen once, and gone.</p>

<h2>What Secure Password Sharing Actually Looks Like</h2>

<p>A proper method for sharing sensitive information should have four properties:</p>

<ul>
  <li><strong>Encryption</strong> — the content should be unreadable to anyone except the intended recipient.</li>
  <li><strong>Self-destruction</strong> — the message should disappear permanently after being viewed, or after a set time.</li>
  <li><strong>No account required</strong> — you shouldn't need to sign up or expose your identity just to send a password.</li>
  <li><strong>No long-term storage</strong> — the data shouldn't sit in a database (or someone's inbox) indefinitely.</li>
</ul>

<p>This is exactly the gap that tools like <a href="https://www.cabinn.in/" target="_blank" rel="noopener noreferrer">Cabin</a> are built to fill.</p>

<h2>How to Share a Password Securely in 3 Steps</h2>

<figure class="article-img-figure">
  <img src="/assets/img/Encrypted self-destructing .webp"
       alt="Encrypted self-destructing note for securely sharing sensitive information"
       class="article-img"
       loading="lazy" width="1200" height="630" />
  <figcaption>Encrypted self-destructing note for securely sharing sensitive information</figcaption>
</figure>

<ol>
  <li>Write your password or sensitive note into a secure, encrypted note tool like Cabin — the content is encrypted with AES-256 before it's even saved.</li>
  <li>Set an expiry or enable <strong>Burn After Read</strong>, so the note is permanently deleted the moment it's opened, or after a time limit like 1 hour or 24 hours.</li>
  <li>Share the generated link through any channel you like — email, WhatsApp, Slack — it doesn't matter, because the link itself only unlocks a note that vanishes after one view.</li>
</ol>

<p>Even if someone intercepts the link later, or the messaging platform is compromised, there's nothing left to find. The password already destroyed itself.</p>

<h2>A Quick Comparison</h2>

<figure class="article-img-figure">
  <img src="/assets/img/Comparison of email.webp"
       alt="Comparison of email, WhatsApp and self-destructing notes for secure password sharing"
       class="article-img"
       loading="lazy" width="1200" height="630" />
  <figcaption>Comparison of email, WhatsApp and self-destructing notes for secure password sharing</figcaption>
</figure>

<div class="article-comparison-table">
  <table>
    <thead>
      <tr>
        <th>Method</th>
        <th>Encrypted</th>
        <th>Self-Destructs</th>
        <th>Needs Sign-Up</th>
        <th>Stays in Chat History</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>Email</strong></td>
        <td class="tbl-no">&#10007; No</td>
        <td class="tbl-no">&#10007; No</td>
        <td class="tbl-neutral">No</td>
        <td class="tbl-no">&#10007; Yes, forever</td>
      </tr>
      <tr>
        <td><strong>WhatsApp</strong></td>
        <td class="tbl-neutral">In transit only</td>
        <td class="tbl-no">&#10007; No</td>
        <td class="tbl-neutral">No</td>
        <td class="tbl-no">&#10007; Yes, in backups</td>
      </tr>
      <tr>
        <td><strong>Cabin (self-destructing note)</strong></td>
        <td class="tbl-yes">&#10003; Yes (AES-256)</td>
        <td class="tbl-yes">&#10003; Yes</td>
        <td class="tbl-yes">&#10003; No</td>
        <td class="tbl-yes">&#10003; No</td>
      </tr>
    </tbody>
  </table>
</div>

<h2>Extra Tips for Sharing Sensitive Data</h2>

<figure class="article-img-figure">
  <img src="/assets/img/Comparison of email .webp"
       alt="Best practices for securely sharing passwords, API keys and sensitive data"
       class="article-img"
       loading="lazy" width="1200" height="630" />
  <figcaption>Best practices for securely sharing passwords, API keys and sensitive data</figcaption>
</figure>

<ul>
  <li>Never send a password and username together in the same message — split them across two different channels if possible.</li>
  <li>Add a password to your note for an extra layer of protection, especially when sharing with someone outside your organisation.</li>
  <li>Use custom short expiry windows — 5 or 15 minutes is usually enough for the recipient to open the link once.</li>
  <li>Avoid recycling the same password across services in the first place — a password manager reduces how often you need to share credentials manually at all.</li>
</ul>

<h2>Final Thought</h2>

<p>Passwords, API keys, and confidential notes deserve better than sitting permanently in an inbox or a chat backup. The safest habit is simple: use a tool built to forget, not one built to remember everything forever.</p>

<figure class="article-img-figure">
  <img src="/assets/img/Self-destructing encrypted .webp"
       alt="Self-destructing encrypted note that disappears after securely sharing a password"
       class="article-img"
       loading="lazy" width="1200" height="630" />
  <figcaption>Self-destructing encrypted note that disappears after securely sharing a password</figcaption>
</figure>

<p>Try creating your first self-destructing, encrypted note free at <a href="https://www.cabinn.in/create" target="_blank" rel="noopener noreferrer">cabinn.in</a> — no sign-up, no logs, gone the moment it's read.</p>

<h2>FAQs</h2>

<div class="article-faq">

  <div class="faq-item">
    <h3 class="faq-question">Can a self-destructing note be accessed again after it's been viewed once?</h3>
    <p class="faq-answer">No. The moment the recipient opens the link in Burn After Read mode, the note is permanently deleted from the database. There is no backup, cache, or recovery option — not even Cabin can retrieve it afterward.</p>
  </div>

  <div class="faq-item">
    <h3 class="faq-question">What happens if the link accidentally ends up with the wrong person?</h3>
    <p class="faq-answer">If password protection is enabled, having the link alone is not enough to open the note — it also requires a password shared separately through a different channel. This extra layer protects you even if the link itself gets exposed.</p>
  </div>

  <div class="faq-item">
    <h3 class="faq-question">Is a note sent through Cabin stored permanently on any server?</h3>
    <p class="faq-answer">No. Notes are encrypted with AES-256 and stored only temporarily, until they expire or are opened in burn-after-read mode. After that, they are wiped completely from the database, with no long-term storage or logging.</p>
  </div>

  <div class="faq-item">
    <h3 class="faq-question">Why is a tool like Cabin better than sending sensitive info over email or WhatsApp?</h3>
    <p class="faq-answer">Emails and WhatsApp messages get stored, backed up, and remain searchable indefinitely, so sensitive information sent years ago could still exist in an inbox or chat backup. Self-destructing note tools are built so sensitive data disappears right after it is seen, leaving no trace behind.</p>
  </div>

  <div class="faq-item">
    <h3 class="faq-question">Do I need to create an account to use Cabin?</h3>
    <p class="faq-answer">No account is required. Cabin is fully anonymous — no sign-up, login, or email verification is needed to create, share, or receive a note, keeping the process fast and private.</p>
  </div>

</div>

<!-- 
  FAQPage Schema (JSON-LD) for cabinn.in
  How to use:
  1. Copy the <script> block below (including the tags).
  2. Paste it inside the <head> of your homepage or FAQ page 
     (or just before the closing </body> tag — both work).
  3. Do NOT change the "@type" or "mainEntity" structure — only edit 
     the question/answer text if you update your FAQs later.
  4. After publishing, test it here: https://search.google.com/test/rich-results
-->

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "Can a self-destructing note be accessed again after it's been viewed once?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "No. The moment the recipient opens the link in Burn After Read mode, the note is permanently deleted from the database. There is no backup, cache, or recovery option — not even Cabin can retrieve it afterward."
      }
    },
    {
      "@type": "Question",
      "name": "What happens if the link accidentally ends up with the wrong person?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "If password protection is enabled, having the link alone is not enough to open the note — it also requires a password shared separately through a different channel. This extra layer protects you even if the link itself gets exposed."
      }
    },
    {
      "@type": "Question",
      "name": "Is a note sent through Cabin stored permanently on any server?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "No. Notes are encrypted with AES-256 and stored only temporarily, until they expire or are opened in burn-after-read mode. After that, they are wiped completely from the database, with no long-term storage or logging."
      }
    },
    {
      "@type": "Question",
      "name": "Why is a tool like Cabin better than sending sensitive info over email or WhatsApp?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Emails and WhatsApp messages get stored, backed up, and remain searchable indefinitely, so sensitive information sent years ago could still exist in an inbox or chat backup. Self-destructing note tools are built so sensitive data disappears right after it is seen, leaving no trace behind."
      }
    },
    {
      "@type": "Question",
      "name": "Do I need to create an account to use Cabin?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "No account is required. Cabin is fully anonymous — no sign-up, login, or email verification is needed to create, share, or receive a note, keeping the process fast and private."
      }
    }
  ]
}
</script>
HTML;

// ─── Insert into database ─────────────────────────────────────────────────────

try {
    $repo = new BlogRepository();

    $existing = $repo->getBySlug('share-passwords-securely-without-email-whatsapp');
    if ($existing) {
        $success = $repo->update($existing->id, [
            'slug'             => 'share-passwords-securely-without-email-whatsapp',
            'title'            => 'How to Share Passwords Securely Without Email or WhatsApp',
            'summary'          => 'Sharing passwords over email or WhatsApp is risky. Learn why, and discover secure, self-destructing alternatives to protect your sensitive data in 2026.',
            'content'          => $content,
            'cover_image'      => '/assets/img/share password securely online.webp',
            'category'         => 'Security',
            'author'           => 'Hussain Lone',
            'read_time'        => '6 min read',
            'status'           => 'published',
            'meta_title'       => 'How to Share Passwords Securely Without Email or WhatsApp',
            'meta_description' => 'Sharing passwords over email or WhatsApp is risky. Learn why, and discover secure, self-destructing alternatives to protect your sensitive data in 2026.',
            'meta_keywords'    => 'share password securely online, secure password sharing, self-destructing notes, AES-256 encryption, Cabin, encrypted notes',
        ]);
        echo $success
            ? "Blog post UPDATED successfully!\n"
            : "Update returned false (no rows changed).\n";
    } else {
        $id = $repo->create([
            'slug'             => 'share-passwords-securely-without-email-whatsapp',
            'title'            => 'How to Share Passwords Securely Without Email or WhatsApp',
            'summary'          => 'Sharing passwords over email or WhatsApp is risky. Learn why, and discover secure, self-destructing alternatives to protect your sensitive data in 2026.',
            'content'          => $content,
            'cover_image'      => '/assets/img/share password securely online.webp',
            'category'         => 'Security',
            'author'           => 'Hussain Lone',
            'read_time'        => '6 min read',
            'status'           => 'published',
            'meta_title'       => 'How to Share Passwords Securely Without Email or WhatsApp',
            'meta_description' => 'Sharing passwords over email or WhatsApp is risky. Learn why, and discover secure, self-destructing alternatives to protect your sensitive data in 2026.',
            'meta_keywords'    => 'share password securely online, secure password sharing, self-destructing notes, AES-256 encryption, Cabin, encrypted notes',
        ]);
        echo "Blog post CREATED with ID: {$id}\n";
    }

    echo "View at: http://localhost:8000/blog/share-passwords-securely-without-email-whatsapp\n";

} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
