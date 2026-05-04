# ============================================
# Installation silencieuse de Notepad++
# ============================================

Write-Host "====================================="
Write-Host " Installation de Notepad++ en cours..."
Write-Host "====================================="
Write-Host ""

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

[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

$downloadUrl = "https://download.notepad-plus-plus.org/repository/8.x/8.6.7/npp.8.6.7.Installer.x64.exe"

$tempPath = "$env:TEMP\npp_installer.exe"

try {
    Write-Host "Téléchargement de Notepad++..."

$webClient = New-Object System.Net.WebClient
$webClient.DownloadFile(
    "https://github.com/notepad-plus-plus/notepad-plus-plus/releases/download/v8.6.6/npp.8.6.6.Installer.x64.exe",
    $tempPath
)

    Write-Host "Téléchargement terminé."
    Write-Host ""

    Write-Host "Lancement de l'installation silencieuse..."
    Start-Process -FilePath $tempPath -ArgumentList "/S" -Wait

    Write-Host ""
    Write-Host " Notepad++ a été installé avec succès."

} catch {
    Write-Host ""
    Write-Host " Erreur pendant l'installation de Notepad++"
    Write-Host $_
}

if (Test-Path $tempPath) {
    Remove-Item $tempPath -Force
}

Write-Host ""
Read-Host "Appuyez sur ENTREE pour fermer"