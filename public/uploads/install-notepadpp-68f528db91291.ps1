# ==========================================
# Installation automatique de Notepad++
# ==========================================

# Forcer l'encodage et TLS 1.2
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
[System.Net.ServicePointManager]::ServerCertificateValidationCallback = { $true }

Write-Host "Téléchargement et installation de Notepad++ en cours..."

# Dossier temporaire
$TempFolder = "$env:TEMP\NotepadPP_Install"
$InstallerPath = "$TempFolder\npp_installer.exe"

if (!(Test-Path $TempFolder)) { New-Item -ItemType Directory -Path $TempFolder | Out-Null }

try {
    Write-Host "Téléchargement de Notepad++..."
    Start-BitsTransfer -Source "https://github.com/notepad-plus-plus/notepad-plus-plus/releases/latest/download/npp.8.6.7.Installer.x64.exe" -Destination $InstallerPath

    Write-Host "Installation en cours..."
    Start-Process -FilePath $InstallerPath -ArgumentList "/S" -Wait

    Write-Host "Installation terminée avec succès."
}
catch {
    Write-Host "Erreur pendant l'installation : $($_.Exception.Message)"
}
finally {
    if (Test-Path $InstallerPath) { Remove-Item $InstallerPath -Force }
    Write-Host "Nettoyage terminé."
    Pause
}
