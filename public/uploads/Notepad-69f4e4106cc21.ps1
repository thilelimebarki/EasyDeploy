# ============================================
# Installation silencieuse de Notepad++
# ============================================

Write-Host "====================================="
Write-Host " Installation de Notepad++ en cours..."
Write-Host "====================================="
Write-Host ""

# Vérification si Notepad++ est déjà installé
$paths = @(
    "C:\Program Files\Notepad++\notepad++.exe",
    "C:\Program Files (x86)\Notepad++\notepad++.exe"
)

foreach ($path in $paths) {
    if (Test-Path $path) {
        Write-Host " Notepad++ est déjà installé sur ce PC."
        Write-Host ""
        Read-Host "Appuyez sur ENTREE pour fermer"
        exit
    }
}

# URL officielle Notepad++
$downloadUrl = "https://github.com/notepad-plus-plus/notepad-plus-plus/releases/latest/download/npp.x64.Installer.exe"

# Chemin temporaire
$tempPath = "$env:TEMP\npp_installer.exe"

try {
    Write-Host "Téléchargement de Notepad++..."
    Invoke-WebRequest -Uri $downloadUrl -OutFile $tempPath -UseBasicParsing

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

# Nettoyage
if (Test-Path $tempPath) {
    Remove-Item $tempPath -Force
}

Write-Host ""
Read-Host "Appuyez sur ENTREE pour fermer"