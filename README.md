# Jenkins_Lab_1
---
## Lab 1 – CI Foundation: Unit Tests, Webhooks & Secrets
## Description
You have a simple PHP web app ( with HTML/CSS/JS frontend and PHP backend)
The code is in a public GitHub repository. You will configure Jenkins to:
- Trigger a build automatically when code is pushed (webhook).
- Run a unit test (PHPUnit) against the PHP logic.
- Store sensitive data (GitHub personal access token) as a secret in Jenkins.
- Fail the build if tests fail.
## Goal of this lab:
1. Create a declarative pipeline (Jenkinsfile)
2. Configure a GitHub webhook to trigger builds on push to a public repo
3. Write and execute a unit test
4. Store and use a GitHub token as a secret in Jenkins
5. Understand build triggers, pipeline syntax, and console output

---
## Tasks: 
1. Prepare the public GitHub repository
2. Install PHPUnit on the Jenkins server
3. Create a Jenkins Pipeline job
4. Store GitHub Personal Access Token in Jenkins credentials (ID: github-token) with scope “Global”.
5. Configure GitHub webhook
6. Write the Jenkinsfile
7. Trigger the build by pushing a commit that breaks a unit test, then fix it and push again to see the automatic build

--- 

#                                          **Instructions**
1. clone this repo $ git clone https://github.com/ahmedsamyabdullah/digi-jenkins-lab.git
2. $ docker exec -u root -it jenkins bash
3. $ apt update
4. $ apt install -y php-cli php-mbstring php-xml php-curl unzip
5. $ wget -O /usr/local/bin/phpunit https://phar.phpunit.de/phpunit-9.phar
6. $ chmod +x /usr/local/bin/phpunit
7. Create GitHub Personal Access Token (PAT)
    - settings -> developer settings -> personal access token -> classic
    - Scopes ( repo  & admin:repo_hook )
    - **Important**: Copy and Save your PAT in external file
8. Store GitHub Personal Access Token in Jenkins credentials (ID: github-token) with scope “Global”.
   - Add Credentials -> secret text 
   - secret (your tokne PAT)
9. Configure GitHub webhook 
   - install github integration plugin on jenkins
   - from your repo -> settings -> webhook -> Add webhook
   - **Note**: for Payload URL use ngrok
10. Ngrok -> login with github accound -> download on windows (host) and get auth
11. open powershell on windows 
    - $ ngrok http http://192.168.x.x:8080    # ip of your ubuntu vm
    - must payload URL on github end by /github-webhook/
    - check Recent Deliveries tab in github webhook is success ping
12. Write the Jenkinsfile
    - # JenBonus
