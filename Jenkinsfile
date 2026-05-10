pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps {
                echo '📥 Cloning repository...'
                checkout scm
            }
        }

        stage('Run Unit Tests') {
            steps {
                echo '🧪 Running PHPUnit tests...'
                sh 'phpunit --testdox tests/'
            }
        }
    }

    post {
        success {
            echo '✅ Build succeeded! All tests passed.'
        }
        failure {
            echo '❌ Build failed! Tests did not pass.'
        }
    }
}