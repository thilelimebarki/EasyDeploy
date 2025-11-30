# ===============================
#  Installation automatique de Notepad++
# ===============================

# Empêche les erreurs liées à l'affichage de la progression
$ProgressPreference = 'SilentlyContinue'

# Force l'utilisation de TLS 1.2 (nécessaire pour GitHub et HTTPS récents)
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

Write-Host ""
Write-Host " Téléchargement et installation de Notepad++ en cours..." -ForegroundColor Cyan

# URL officielle du setup Notepad++
$installerUrl = "https://github.com/notepad-plus-plus/notepad-plus-plus/releases/latest/download/npp.8.6.7.Installer.x64.exe"

# Dossier temporaire pour stocker le fichier d’installation
$tempFolder = "$env:TEMP\NotepadPPInstall"
$installerPath = "$tempFolder\npp_installer.exe"

# Crée le dossier temporaire si nécessaire
if (!(Test-Path -Path $tempFolder)) {
    New-Item -ItemType Directory -Path $tempFolder | Out-Null
}

try {
    # Téléchargement du setup depuis GitHub
    Write-Host " Téléchargement de Notepad++..." -ForegroundColor Yellow
    Invoke-WebRequest -Uri $installerUrl -OutFile $installerPath -UseBasicParsing
    Write-Host " Téléchargement terminé !" -ForegroundColor Green

    # Exécution silencieuse (/S = installation sans interface)
    Write-Host "  Installation en cours..." -ForegroundColor Yellow
    Start-Process -FilePath $installerPath -ArgumentList "/S" -Wait

    Write-Host " Installation de Notepad++ terminée avec succès !" -ForegroundColor Green
}
catch {
    Write-Host " Erreur pendant l'installation : $($_.Exception.Message)" -ForegroundColor Red
}
finally {
    # Nettoyage
    if (Test-Path $installerPath) {
        Remove-Item $installerPath -Force
    }
    Write-Host " Nettoyage terminé."
}
