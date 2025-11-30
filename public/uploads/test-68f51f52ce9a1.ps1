# ==============================
# ✅ Script de TEST EasyDeploy
# ==============================
# Ce script ne fait rien de dangereux. Il affiche une confirmation
# et écrit un fichier de log sur la machine pour valider que l'exécution fonctionne.

# Création d'un dossier de log temporaire
$logDir = "$env:TEMP\easydeploy_test"
if (!(Test-Path -Path $logDir)) {
    New-Item -Path $logDir -ItemType Directory -Force | Out-Null
}

# Nom du fichier log
$logFile = Join-Path $logDir ("install_log_{0}.txt" -f (Get-Date -Format "yyyyMMdd_HHmmss"))

# Message de test
$msg = "✅ Installation DE TEST exécutée avec succès !
🖥 Machine : $env:COMPUTERNAME
👤 Utilisateur : $env:USERNAME
⏰ Date : $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"

# Essaye d'afficher une fenêtre (boîte de message)
try {
    Add-Type -AssemblyName PresentationFramework
    [System.Windows.MessageBox]::Show($msg, "EasyDeploy - Test", "OK", "Information") | Out-Null
} catch {
    Write-Host "Mode sans interface graphique détecté — affichage console uniquement."
    Write-Host $msg
}

# Écriture dans un log
try {
    $msg | Out-File -FilePath $logFile -Encoding UTF8
} catch {
    Write-Host "⚠ Impossible d'écrire le fichier de log. Erreur : $_"
}

# Pause visuelle (facultatif)
Start-Sleep -Seconds 2

Write-Host "🎉 Test terminé — le script a bien été exécuté !"
