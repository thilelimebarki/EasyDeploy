# ===============================
#  Installation automatique de Notepad++
# ===============================

# Empêche les erreurs liées au flux de progression
$ProgressPreference = 'SilentlyContinue'

Write-Host "Téléchargement et installation de Notepad++ en cours..." -ForegroundColor Cyan

# URL officielle du setup Notepad++
$installerUrl = "https://github.com/notepad-plus-plus/notepad-plus-plus/releases/latest/download/npp.8.6.7.Installer.x64.exe"

# Dossier temporaire pour le téléchargement
$tempFolder = "$env:TEMP\NotepadPPInstall"
$installerPath = "$tempFolder\npp_installer.exe"

# Création du dossier si nécessaire
if (!(Test-Path -Path $tempFolder)) {
    New-Item -ItemType Directory -Path $tempFolder | Out-Null
}

try {
    # Téléchargement de l'installeur
    Invoke-WebRequest -Uri $installerUrl -OutFile $installerPath -UseBasicParsing
    Write-Host "Téléchargement terminé : $installerPath" -ForegroundColor Green

    # Exécution de l'installation silencieuse
    Write-Host " Installation en cours..."
    Start-Process -FilePath $installerPath -ArgumentList "/S" -Wait

    Write-Host " Installation de Notepad++ terminée avec succès !" -ForegroundColor Green
}
catch {
    Write-Host " Erreur pendant l'installation : $($_.Exception.Message)" -ForegroundColor Red
}
finally {
    # Nettoyage des fichiers temporaires
    if (Test-Path $installerPath) {
        Remove-Item $installerPath -Force
    }
}
