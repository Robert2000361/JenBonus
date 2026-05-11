<div align="center">

# ⚙️ JenBonus — CI/CD Pipeline with Jenkins

### A Production-Style Jenkins CI/CD Lab: Webhooks, Unit Tests & Secrets

[![Jenkins](https://img.shields.io/badge/Jenkins-2.555.1-D24939?style=for-the-badge&logo=jenkins&logoColor=white)](https://www.jenkins.io/)
[![PHP](https://img.shields.io/badge/PHP-8.5-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![PHPUnit](https://img.shields.io/badge/PHPUnit-9.6-366488?style=for-the-badge&logo=php&logoColor=white)](https://phpunit.de/)
[![Docker](https://img.shields.io/badge/Docker-Jenkins%20LTS-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://hub.docker.com/r/jenkins/jenkins)
[![GitHub](https://img.shields.io/badge/GitHub-Webhook-181717?style=for-the-badge&logo=github&logoColor=white)](https://github.com/)
[![zrok](https://img.shields.io/badge/zrok-Tunnel-6D28D9?style=for-the-badge)](https://zrok.io/)

*A real PHP web application — service ordering and subscription management — wired into a fully automated CI/CD pipeline that tests, fails, and recovers on every single git push.*

</div>

---

## 📑 Table of Contents

1. [Project Overview](#-project-overview)
2. [Architecture & Mental Model](#-architecture--mental-model)
3. [Repository Structure](#-repository-structure)
4. [CI/CD Flow — Step by Step](#-cicd-flow--step-by-step)
5. [Prerequisites](#-prerequisites)
6. [Setup Guide](#-setup-guide)
   - [Step 1 — Jenkins on Docker](#step-1--jenkins-on-docker)
   - [Step 2 — Install PHPUnit Inside Jenkins](#step-2--install-phpunit-inside-jenkins)
   - [Step 3 — Store GitHub PAT as Jenkins Secret](#step-3--store-github-pat-as-jenkins-secret)
   - [Step 4 — Create the Jenkins Pipeline Job](#step-4--create-the-jenkins-pipeline-job)
   - [Step 5 — Expose Jenkins with zrok](#step-5--expose-jenkins-with-zrok)
   - [Step 6 — Configure GitHub Webhook](#step-6--configure-github-webhook)
7. [The Jenkinsfile Explained](#-the-jenkinsfile-explained)
8. [Unit Tests Reference](#-unit-tests-reference)
9. [Break It & Fix It — The Core Lab Task](#-break-it--fix-it--the-core-lab-task)
10. [Troubleshooting](#-troubleshooting)

---

## 🎯 Project Overview

This lab demonstrates the **foundation of Continuous Integration** using Jenkins. The application is a PHP service platform where users can place service orders and manage subscriptions. Every `git push` automatically:

| Trigger | Action | Result |
|---------|--------|--------|
| `git push` to `main` | GitHub fires Webhook | Jenkins Pipeline starts |
| Pipeline starts | Checkout stage clones the repo | Code is ready on the agent |
| Checkout done | PHPUnit runs all tests | Pass ✅ or Fail ❌ |
| Tests pass | Build marked **SUCCESS** | Green build in Jenkins |
| Tests fail | Build marked **FAILURE** | Red build — developer is notified |

**The key skill demonstrated:** You never manually run Jenkins. One `git push` does everything.

---

## 🏛 Architecture & Mental Model

> **📸 Architecture Diagram**
>
> <!-- INSERT YOUR ARCHITECTURE IMAGE HERE -->
> <!-- Example: ![CI/CD Architecture](./assets/architecture.png) -->
> *Replace this comment with your architecture image using:*
> `![Architecture](./path/to/your/image.png)`

```
  Developer (VM)
       │
       │  git push origin main
       ▼
  ┌─────────────┐        POST /github-webhook/
  │   GitHub    │ ──────────────────────────────────────▶ zrok Public URL
  │  (JenBonus) │                                              │
  │  Webhook ✓  │                                              │ tunnel
  └─────────────┘                                              ▼
                                                    ┌─────────────────────┐
                                                    │   Ubuntu VM          │
                                                    │  ┌───────────────┐   │
                                                    │  │    Docker     │   │
                                                    │  │  ┌─────────┐  │   │
                                                    │  │  │ Jenkins │  │   │
                                                    │  │  │  :8080  │  │   │
                                                    │  │  │         │  │   │
                                                    │  │  │ PHP CLI │  │   │
                                                    │  │  │ PHPUnit │  │   │
                                                    │  │  └────┬────┘  │   │
                                                    │  └───────┼───────┘   │
                                                    └──────────┼───────────┘
                                                               │
                                                    ┌──────────▼──────────┐
                                                    │   Pipeline Stages    │
                                                    │                      │
                                                    │  1. Checkout  📥     │
                                                    │  2. PHPUnit   🧪     │
                                                    │                      │
                                                    │  ✅ SUCCESS          │
                                                    │     or               │
                                                    │  ❌ FAILURE          │
                                                    └──────────────────────┘
```

### Component Summary

| Component | Technology | Role |
|-----------|------------|------|
| Jenkins Master | Docker Container (jenkins/jenkins:lts) | Orchestrates and runs the pipeline |
| PHP Runtime | PHP 8.5 CLI (installed inside Jenkins container) | Executes the application logic |
| PHPUnit | PHPUnit 9.6 (installed inside Jenkins container) | Runs automated unit tests |
| zrok | Reverse tunnel (v2.0.3) | Exposes Jenkins to GitHub Webhooks |
| GitHub | Public Repository + Webhook | Source of truth + pipeline trigger |
| PAT Secret | Jenkins Credentials Store | Secure GitHub authentication |

---

## 📁 Repository Structure

```
JenBonus/
│
├── src/
│   ├── OrderProcessor.php        # Business logic — validates and processes service orders
│   └── SubscriptionManager.php   # Business logic — calculates subscription days remaining
│
├── tests/
│   ├── OrderTest.php             # PHPUnit tests for OrderProcessor
│   └── SubscriptionTest.php      # PHPUnit tests for SubscriptionManager
│
├── index.php                     # Frontend — HTML/CSS/JS service request form
├── Jenkinsfile                   # Pipeline definition — the brain of CI/CD
└── README.md                     # This file
```

---

## 🔄 CI/CD Flow — Step by Step

```
Step 1  →  Developer runs: git push origin main
Step 2  →  GitHub detects the push event
Step 3  →  GitHub sends POST request to: https://xxxx.shares.zrok.io/github-webhook/
Step 4  →  zrok tunnel receives request → forwards to localhost:8080 (Jenkins)
Step 5  →  Jenkins reads the Jenkinsfile from the repository
Step 6  →  Stage 1 [Checkout]  — Jenkins clones the repository
Step 7  →  Stage 2 [Run Tests] — PHPUnit executes tests/ directory
Step 8a →  All tests pass  → Build SUCCESS ✅  (green)
Step 8b →  Any test fails  → Build FAILURE ❌  (red)
```

---

## 🛠 Prerequisites

| Tool | Purpose | Verify |
|------|---------|--------|
| Docker | Runs Jenkins container | `docker --version` |
| Git | Push code to GitHub | `git --version` |
| GitHub Account | Repository + Webhook + PAT | — |
| zrok Account | Free tunnel for Webhook | `zrok version` |
| Ubuntu VM | Host environment | — |

---

## 🚀 Setup Guide

### Step 1 — Jenkins on Docker

```bash
# Pull and run Jenkins
docker run -d \
  --name jenkins \
  -p 8080:8080 \
  -p 50000:50000 \
  -v jenkins_home:/var/jenkins_home \
  jenkins/jenkins:lts

# Get the initial admin password
docker exec jenkins cat /var/jenkins_home/secrets/initialAdminPassword
```

Open `http://localhost:8080`, paste the password, and install **suggested plugins**.

---

### Step 2 — Install PHPUnit Inside Jenkins

```bash
# Enter the Jenkins container as root
docker exec -u root -it jenkins bash

# Add a working Debian mirror
cat > /etc/apt/sources.list << 'EOF'
deb https://mirror.init7.net/debian/ trixie main
EOF

# Install PHP
apt update -o Acquire::Check-Valid-Until=false && \
apt install -y php-cli php-mbstring php-xml php-curl unzip

# Install PHPUnit
curl -L https://phar.phpunit.de/phpunit-9.phar -o /usr/local/bin/phpunit
chmod +x /usr/local/bin/phpunit

# Verify
php -v
phpunit --version
```

---

### Step 3 — Store GitHub PAT as Jenkins Secret

**Create a GitHub Personal Access Token (PAT):**
```
GitHub → Settings → Developer Settings →
Personal Access Tokens → Tokens (classic) → Generate new token

Scopes required:
  ✅ repo
  ✅ admin:repo_hook
```

> ⚠️ **Copy and save the token immediately** — GitHub will never show it again.

**Store in Jenkins:**
```
Manage Jenkins → Credentials → System →
Global credentials → Add Credentials

  Kind:        Secret text
  Scope:       Global
  Secret:      <your PAT>
  ID:          github-token
  Description: GitHub PAT
```

---

### Step 4 — Create the Jenkins Pipeline Job

```
New Item → Pipeline → OK

Under "Build Triggers":
  ✅ GitHub hook trigger for GITScm polling

Under "Pipeline":
  Definition:       Pipeline script from SCM
  SCM:              Git
  Repository URL:   https://github.com/YOUR_USERNAME/JenBonus.git
  Credentials:      github-token
  Branch:           */main
  Script Path:      Jenkinsfile
```

---

### Step 5 — Expose Jenkins with zrok

```bash
# Install zrok (if not already installed)
curl -s https://api.github.com/repos/openziti/zrok/releases/latest | grep tag_name
curl -L https://github.com/openziti/zrok/releases/download/vX.X.X/zrok_X.X.X_linux_amd64.tar.gz -o zrok.tar.gz
tar -xzf zrok.tar.gz
sudo mv zrok /usr/local/bin/zrok

# Enable your zrok account (get token from zrok.io)
zrok enable <YOUR_TOKEN>

# Share Jenkins publicly
zrok share public http://localhost:8080
# Copy the generated URL → https://xxxx.shares.zrok.io
```

> ⚠️ **Important:** zrok URL changes every session. Update your GitHub Webhook whenever you restart zrok.

---

### Step 6 — Configure GitHub Webhook

```
GitHub Repo → Settings → Webhooks → Add webhook

  Payload URL:   https://xxxx.shares.zrok.io/github-webhook/
  Content type:  application/json
  Events:        Just the push event

→ Add webhook
```

**Verify under Recent Deliveries — you should see a green ✅ ping.**

---

## 📄 The Jenkinsfile Explained

```groovy
pipeline {
    agent any          // Run on Jenkins itself (Built-in node)

    stages {
        stage('Checkout') {
            steps {
                echo 'Cloning repository...'
                checkout scm   // Pulls the repo that triggered this build
            }
        }

        stage('Run Unit Tests') {
            steps {
                echo 'Running PHPUnit tests...'
                sh 'phpunit --testdox tests/'   // Runs ALL tests in tests/ directory
            }
        }
    }

    post {
        success {
            echo 'Build succeeded! All tests passed.'   // Green build
        }
        failure {
            echo 'Build failed! Tests did not pass.'    // Red build
        }
    }
}
```

---

## 🧪 Unit Tests Reference

### OrderProcessor Tests (`tests/OrderTest.php`)

| Test | Input | Expected |
|------|-------|----------|
| `testOrderProcessingSuccess` | Name: "Ahmed", Service: "DevOps Automation" | `status: success` |
| `testOrderProcessingInvalidName` | Name: "Ab" (too short), Service: "Cloud Migration" | `status: error` |

**Business rules enforced:**
- Name must be at least 3 characters
- Service must be one of: `Web Development`, `DevOps Automation`, `Cloud Migration`

---

### SubscriptionManager Tests (`tests/SubscriptionTest.php`)

| Test | Input | Expected |
|------|-------|----------|
| `testDaysRemainingCalculation` | Total: 30 days, Used: 10 days | `20` days remaining |
| `testExpiredSubscription` | Total: 30 days, Used: 35 days | `0` (expired) |

---

## 💥 Break It & Fix It — The Core Lab Task

This is the main exercise that proves the pipeline works end-to-end.

### Phase 1 — Break the Test (expect ❌ red build)

Edit `tests/OrderTest.php`:

```php
// Change this:
$this->assertEquals("success", $response['status']);

// To this (intentional wrong value):
$this->assertEquals("failure", $response['status']);
```

```bash
git add .
git commit -m "break: intentional test failure"
git push origin main
```

**Expected result:** Jenkins detects the push → runs tests → **BUILD FAILED ❌**

---

### Phase 2 — Fix the Test (expect ✅ green build)

```php
// Restore the correct assertion:
$this->assertEquals("success", $response['status']);
```

```bash
git add .
git commit -m "fix: restore correct assertion"
git push origin main
```

**Expected result:** Jenkins detects the push → runs tests → **BUILD SUCCESS ✅**

---

## ⚠️ Troubleshooting

| Symptom | Root Cause | Fix |
|---------|------------|-----|
| **Webhook shows "couldn't deliver"** | Jenkins unreachable or zrok not running | Restart zrok and update Webhook URL |
| **Build doesn't trigger on push** | `GitHub hook trigger for GITScm polling` not checked | Enable it in Pipeline Configure → Build Triggers |
| **`phpunit: command not found`** | PHPUnit not installed inside container | Re-run Step 2 — install inside container as root |
| **`apt update` fails with 403** | Debian default mirror blocked by network | Use `mirror.init7.net` mirror (see Step 2) |
| **Agent offline** | EC2 or external agent disconnected | Switch to `agent any` to use Built-in node |
| **Authentication failed on push** | GitHub no longer accepts passwords | Use PAT in remote URL: `https://TOKEN@github.com/user/repo.git` |
| **zrok URL expired** | zrok generates new URL each session | Run `zrok share public http://localhost:8080` again and update Webhook |
| **Build stays "waiting to schedule"** | Wrong agent label in Jenkinsfile | Use `agent any` instead of `agent { label 'ec2-agent' }` |

---

<div align="center">

**JenBonus** — Jenkins CI/CD Foundation Lab

*Every push tells a story. Make sure yours ends with a green build.* ✅

Built with ☕ and way too many `git push` commands.

</div>








# zrok - Secure internet sharing made simple

![zrok logo](docs/images/zrok_cover.png)

**Share anything, anywhere, instantly. Enterprise reliability. No firewall changes. No port forwarding. No hassle.**

zrok lets you securely share web services, files, and network resources with anyone—whether they're across the internet or your private network. Built on zero-trust networking, it works through firewalls and NAT without requiring any network configuration changes.

## Quick start

Get sharing in under 2 minutes:

1. **[Install zrok](https://docs.zrok.io/docs/guides/install/)** for your platform
2. **Get an account**: `zrok invite` (use the free [zrok.io service](https://docs.zrok.io/docs/getting-started/))
3. **Enable sharing**: `zrok enable`

That's it! Now you can share anything:

```bash
# Share a web service publicly
$ zrok share public localhost:8080

# Share files as a network drive  
$ zrok share public --backend-mode drive ~/Documents

# Share privately with other zrok users
$ zrok share private localhost:3000
```

![zrok Web Console](docs/images/zrok_web_console.png)

## What you can share

### Web services

Instantly make local web apps accessible over the internet:

```bash
zrok share public localhost:8080
```

![zrok share public](docs/images/zrok_share_public.png)

### Files & directories

Turn any folder into a shareable network drive:

```bash
zrok share public --backend-mode drive ~/Repos/zrok
```

![zrok share public -b drive](docs/images/zrok_share_public_drive.png)
![mounted zrok drive](docs/images/zrok_share_public_drive_explorer.png)

### Private resources

Share TCP/UDP services securely with other zrok users—no public internet exposure.

## Key features

- **Zero Configuration**: Works through firewalls, NAT, and corporate networks
- **Secure by Default**: End-to-end encryption with zero-trust architecture  
- **Public & Private Sharing**: Share with anyone or just specific users
- **Multiple Protocols**: HTTP/HTTPS, TCP, UDP, and file sharing
- **Cross-Platform**: Windows, macOS, Linux, and Raspberry Pi
- **Self-Hostable**: Run your own zrok service instance

## How it works

zrok is built on [OpenZiti](https://docs.openziti.io/docs/learn/introduction/), a programmable zero-trust network overlay. This means:

- **No inbound connectivity required**: Works from behind firewalls and NAT
- **End-to-end encryption**: All traffic is encrypted, even from zrok servers
- **Peer-to-peer connections**: Direct connections between users when possible
- **Identity-based access**: Share with specific users, not IP addresses

## Developer SDK

Embed zrok sharing into your applications with our Go SDK:

```go
// Create a share
shr, err := sdk.CreateShare(root, &sdk.ShareRequest{
    BackendMode: sdk.TcpTunnelBackendMode,
    ShareMode:   sdk.PrivateShareMode,
})

// Accept connections
listener, err := sdk.NewListener(shr.Token, root)
```

[Read the SDK guide](https://blog.openziti.io/the-zrok-sdk) for complete examples.

## Self-hosting

Run your own zrok service—from Raspberry Pi to enterprise scale:

- Single binary contains everything you need
- Scales from small personal instances to large public services
- Built on the same codebase as the public zrok.io service

[Self-Hosting Guide](https://docs.zrok.io/docs/guides/self-hosting/self_hosting_guide/)

## Resources

- **[Documentation](https://docs.zrok.io/)**
- **[Office Hours Videos](https://www.youtube.com/watch?v=Edqv7yRmXb0&list=PLMUj_5fklasLuM6XiCNqwAFBuZD1t2lO2)**
- **[Building from source](./BUILD.md)**
- **[Contributing](./CONTRIBUTING.md)**

---

*Ready to start sharing? [Get started with zrok →](https://docs.zrok.io/docs/getting-started)*
# test webhook
# trigger
