# ============================================
# Installation silencieuse de 7-Zip (64 bits)
# ============================================

Write-Host "====================================="
Write-Host " Installation de 7-Zip en cours..."
Write-Host "====================================="
Write-Host ""

# Vérification si 7-Zip est déjà installé
$sevenZipPath = "C:\Program Files\7-Zip\7z.exe"

if (Test-Path $sevenZipPath) {
    Write-Host " 7-Zip est déjà installé sur ce PC."
    Write-Host ""
    Read-Host "Appuyez sur ENTREE pour fermer"
    exit
}

# URL officielle 7-Zip (64 bits)
$downloadUrl = "https://www.7-zip.org/a/7z2301-x64.exe"

# Chemin temporaire
$tempPath = "$env:TEMP\7zip_installer.exe"

try {
    Write-Host "Téléchargement de 7-Zip..."
    Invoke-WebRequest -Uri $downloadUrl -OutFile $tempPath -UseBasicParsing

    Write-Host "Téléchargement terminé."
    Write-Host ""

    Write-Host "Lancement de l'installation silencieuse..."
    Start-Process -FilePath $tempPath -ArgumentList "/S" -Wait

    Write-Host ""
    Write-Host " 7-Zip a été installé avec succès."

} catch {
    Write-Host ""
    Write-Host " Erreur pendant l'installation de 7-Zip"
    Write-Host $_
}

# Nettoyage
if (Test-Path $tempPath) {
    Remove-Item $tempPath -Force
}

Write-Host ""
Read-Host "Appuyez sur ENTREE pour fermer"