#!/bin/bash
DIR="$(dirname "$(readlink -f ${BASH_SOURCE[0]})")"
TMPDIR=/tmp/tmp.$(( $RANDOM * 19318203981230 + 40 ))
PLUGIN=$(basename ${DIR})
ARCHIVE="$(dirname $(dirname ${DIR}))/archive"
DESTDIR="$TMPDIR/usr/local/emhttp/plugins/${PLUGIN}"
PLG_FILE="$(dirname $(dirname ${DIR}))/plugins/${PLUGIN}.plg"
VERSION=$(date +"%Y.%m.%d")
PACKAGE="${ARCHIVE}/${PLUGIN}-%s.txz"
MD5="${ARCHIVE}/${PLUGIN}-%s.md5"

for x in '' a b c d e d f g h ; do
  pkg=$(printf "$PACKAGE" "${VERSION}${x}")
  if [[ ! -f "$pkg" ]]; then
    PACKAGE=$pkg
    MD5=$(printf "$MD5" "${VERSION}${x}")
    VERSION="${VERSION}${x}"
    break
  fi
done

sed -i -e "s#\(ENTITY\s*version[^\"]*\).*#\1\"${VERSION}\">#" "$PLG_FILE"

mkdir -p "${DESTDIR}/"
cd "$DIR"
cp --parents -f $(find . -type f ! \( -iname "pkg_build.sh" -o -iname "sftp-config.json" -o -iname ".DS_Store"  \) ) "${DESTDIR}/"
cd "$TMPDIR/"
makepkg -l y -c y "${PACKAGE}"
cd "$ARCHIVE/"
md5sum $(basename "$PACKAGE") > "$MD5"
rm -rf "$TMPDIR"

# Verify and install plugin package
sum1=$(md5sum "${PACKAGE}")
sum2=$(cat "$MD5")
if [ "${sum1:0:32}" != "${sum2:0:32}" ]; then
  echo "Checksum mismatched.";
  rm "$MD5" "${PACKAGE}"
else
  echo "Checksum matched."
fi
