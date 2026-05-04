# ============================================
# Installation silencieuse de Notepad++
# ============================================

Write-Host "====================================="
Write-Host " Installation de Notepad++ en cours..."
Write-Host "====================================="
Write-Host ""

# Vérification installation
$paths = @(
    "C:\Program Files\Notepad++\notepad++.exe",
    "C:\Program Files (x86)\Notepad++\notepad++.exe"
)

foreach ($path in $paths) {
    if (Test-Path $path) {
        Write-Host " Notepad++ est déjà installé."
        Read-Host "Appuyez sur ENTREE pour fermer"
        exit
    }
}

# FORCER TLS 1.2 (IMPORTANT)
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

# URL Notepad++
$downloadUrl = "https://github.com/notepad-plus-plus/notepad-plus-plus/releases/latest/download/npp.x64.Installer.exe"

$tempPath = "$env:TEMP\npp_installer.exe"

try {
    Write-Host "Téléchargement de Notepad++..."
    Invoke-WebRequest -Uri $downloadUrl -OutFile $tempPath

    Write-Host "Téléchargement terminé."
    Write-Host ""

    Write-Host "Installation en cours..."
    Start-Process -FilePath $tempPath -ArgumentList "/S" -Wait

    Write-Host ""
    Write-Host " Notepad++ installé avec succès."

} catch {
    Write-Host ""
    Write-Host " Erreur pendant l'installation"
    Write-Host $_
}

# Nettoyage
if (Test-Path $tempPath) {
    Remove-Item $tempPath -Force
}

Write-Host ""
Read-Host "Appuyez sur ENTREE pour fermer"